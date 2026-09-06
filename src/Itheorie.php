<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi;

use Emeq\ItheorieApi\Contracts\ItheorieCredentialResolver;
use Emeq\ItheorieApi\Contracts\TokenStore;
use Emeq\ItheorieApi\Data\ItheorieCredentials;
use Emeq\ItheorieApi\Data\PurchaseRequest;
use Emeq\ItheorieApi\Enums\ErrorKind;
use Emeq\ItheorieApi\Exceptions\ItheorieException;
use Emeq\ItheorieApi\Http\ItheorieConnector;
use Emeq\ItheorieApi\Http\Request\GetAuthToken;
use Emeq\ItheorieApi\Http\Request\Read\GetCourse;
use Emeq\ItheorieApi\Http\Request\Read\GetCourses;
use Emeq\ItheorieApi\Http\Request\Read\GetPurchase;
use Emeq\ItheorieApi\Http\Request\Read\GetPurchases;
use Emeq\ItheorieApi\Http\Request\Read\GetStudent;
use Emeq\ItheorieApi\Http\Request\Read\GetStudentDetailed;
use Emeq\ItheorieApi\Http\Request\Write\CreatePurchase;
use Emeq\ItheorieApi\Support\ErrorMapper;
use Emeq\ItheorieApi\Support\Normalize;
use Emeq\ItheorieApi\Support\PurchaseLocation;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Request;
use Saloon\Http\Response;

class Itheorie
{
    private const LOCK_WAIT_SECONDS = 10;

    private const LOCK_TTL_SECONDS = 30;

    private ?ItheorieConnector $connector = null;

    private ?ItheorieCredentials $credentials = null;

    public function __construct(
        private readonly ItheorieCredentialResolver $resolver,
        private readonly TokenStore $tokens,
        private readonly ?LockProvider $locks = null,
    ) {}

    public function credentials(): ItheorieCredentials
    {
        return $this->credentials ??= $this->resolver->resolve();
    }

    public function connector(): ItheorieConnector
    {
        return $this->connector ??= new ItheorieConnector($this->credentials());
    }

    /**
     * @return array{links: array<string, string|null>, data: list<array<string, mixed>>}
     */
    public function courses(int $page = 1, int $limit = 50): array
    {
        $data = $this->json($this->send(new GetCourses($this->reseller(), $page, $limit)));

        return Normalize::collection($data, Normalize::course(...));
    }

    /**
     * @return array<string, mixed>
     */
    public function course(string $course): array
    {
        return Normalize::course($this->json($this->send(new GetCourse($this->reseller(), $course))));
    }

    /**
     * @return array{links: array<string, string|null>, data: list<array<string, mixed>>}
     */
    public function purchases(int $page = 1, int $limit = 50): array
    {
        $data = $this->json($this->send(new GetPurchases($this->reseller(), $page, $limit)));

        return Normalize::collection($data, Normalize::purchase(...));
    }

    /**
     * @return array<string, mixed>
     */
    public function purchase(string $purchase): array
    {
        return Normalize::purchase($this->json($this->send(new GetPurchase($this->reseller(), $purchase))));
    }

    /**
     * @return array<string, mixed>
     */
    public function createPurchase(PurchaseRequest $request): array
    {
        $response = $this->send(new CreatePurchase($this->reseller(), $request));
        $data = $this->json($response);

        if (($data['id'] ?? null) !== null) {
            return Normalize::purchase($data);
        }

        $location = $response->header('Location');
        $id = is_string($location) ? PurchaseLocation::idFrom($location) : null;

        if ($id === null) {
            throw new ItheorieException(
                message: 'iTheorie bevestigde de aankoop zonder bruikbare verwijzing.',
                kind: ErrorKind::Unknown,
                status: $response->status(),
                partnerCode: 0,
            );
        }

        return $this->purchase($id);
    }

    /**
     * @return array<string, mixed>
     */
    public function student(string $accessCode): array
    {
        return Normalize::subscription($this->json($this->send(new GetStudent($this->reseller(), $accessCode))));
    }

    /**
     * @return array<string, mixed>
     */
    public function studentDetailed(string $accessCode): array
    {
        return Normalize::subscription($this->json($this->send(new GetStudentDetailed($this->reseller(), $accessCode))));
    }

    public function token(): string
    {
        return $this->tokens->get($this->credentials()) ?? $this->authenticate();
    }

    private function reseller(): string
    {
        return $this->credentials()->reseller;
    }

    private function send(Request $request, bool $retried = false): Response
    {
        $token = $this->token();
        $response = $this->connector()->send($request->authenticate(new TokenAuthenticator($token)));

        // Een geslaagde aankoop antwoordt met 303 naar de aankooppagina, dus een
        // redirect is hier succes en geen fout.
        if ($response->successful() || $response->redirect()) {
            return $response;
        }

        $exception = ErrorMapper::fromResponse($response);

        if (! $retried && $exception->isStaleToken()) {
            $this->authenticate($token);

            return $this->send($request, true);
        }

        throw $exception;
    }

    /**
     * iTheorie trekt het vorige token in zodra er een nieuw wordt aangemaakt, dus
     * twee processen die tegelijk vernieuwen slaan elkaars token om. De lock laat
     * er één winnen; de verliezers lezen onder de lock het verse token.
     */
    private function authenticate(?string $stale = null): string
    {
        if ($this->locks === null) {
            return $this->requestToken();
        }

        $lock = $this->locks->lock('itheorie:auth:'.$this->credentials()->fingerprint(), self::LOCK_TTL_SECONDS);

        try {
            $lock->block(self::LOCK_WAIT_SECONDS);
        } catch (LockTimeoutException) {
            throw new ItheorieException(
                message: 'Kon geen iTheorie-token vernieuwen: de refresh-lock bleef bezet.',
                kind: ErrorKind::ServiceUnavailable,
                status: 503,
                partnerCode: 0,
            );
        }

        try {
            $fresh = $this->tokens->get($this->credentials());

            if ($fresh !== null && $fresh !== $stale) {
                return $fresh;
            }

            return $this->requestToken();
        } finally {
            $lock->release();
        }
    }

    private function requestToken(): string
    {
        $response = $this->connector()->send(new GetAuthToken($this->credentials()));

        if ($response->failed()) {
            throw ErrorMapper::fromResponse($response);
        }

        $token = $response->json('token');

        if (! is_string($token) || $token === '') {
            throw new ItheorieException(
                message: 'iTheorie gaf een authenticatie-antwoord zonder token.',
                kind: ErrorKind::Authentication,
                status: $response->status(),
                partnerCode: 0,
            );
        }

        $this->tokens->put($this->credentials(), $token);

        return $token;
    }

    /**
     * @return array<string, mixed>
     */
    private function json(Response $response): array
    {
        $data = $response->json();

        return is_array($data) ? $data : [];
    }
}
