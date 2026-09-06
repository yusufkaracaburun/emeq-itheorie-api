<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Testing;

use Emeq\ItheorieApi\Data\PurchaseRequest;
use Emeq\ItheorieApi\Enums\ErrorKind;
use Emeq\ItheorieApi\Exceptions\ItheorieException;
use Emeq\ItheorieApi\Itheorie;
use Emeq\ItheorieApi\Support\Normalize;

final class FakeItheorie extends Itheorie
{
    /** @var array<string, array<string, mixed>> */
    private array $purchases = [];

    /** @var list<array<string, mixed>> */
    private array $courses;

    public function __construct()
    {
        $this->courses = [
            $this->fakeCourse('fake-course-b', 'B', 'nl', 'Auto'),
            $this->fakeCourse('fake-course-a', 'A', 'nl', 'Motor'),
            $this->fakeCourse('fake-course-am', 'AM', 'nl', 'Bromfiets'),
        ];
    }

    public function courses(int $page = 1, int $limit = 50): array
    {
        return Normalize::collection(['data' => $this->courses], Normalize::course(...));
    }

    public function course(string $course): array
    {
        foreach ($this->courses as $candidate) {
            if (($candidate['id'] ?? null) === $course) {
                return Normalize::course($candidate);
            }
        }

        throw new ItheorieException('Course could not be found', ErrorKind::NotFound, 404, 404004);
    }

    public function purchases(int $page = 1, int $limit = 50): array
    {
        return Normalize::collection(['data' => array_values($this->purchases)], Normalize::purchase(...));
    }

    public function purchase(string $purchase): array
    {
        if (! isset($this->purchases[$purchase])) {
            throw new ItheorieException('Purchase could not be found', ErrorKind::NotFound, 404, 404008);
        }

        return Normalize::purchase($this->purchases[$purchase]);
    }

    public function createPurchase(PurchaseRequest $request): array
    {
        $id = 'fake-purchase-'.bin2hex(random_bytes(6));
        $accessCode = strtoupper(substr(bin2hex(random_bytes(8)), 0, 14));

        $this->purchases[$id] = [
            'id' => $id,
            'invoice' => null,
            'createdAt' => date(DATE_ATOM),
            'price' => ['amount' => '9.70', 'currency' => 'EUR'],
            'subscription' => 'fake-subscription-'.bin2hex(random_bytes(4)),
            'accessCode' => $accessCode,
            'expiresAt' => null,
            'loginUrl' => 'https://itheorie.nl/login',
            'directLoginUrl' => 'https://itheorie.nl/login/'.$accessCode,
            'name' => $request->name,
            'email' => $request->email,
            'mobilePhoneNumber' => $request->mobilePhone,
        ];

        return Normalize::purchase($this->purchases[$id]);
    }

    public function student(string $accessCode): array
    {
        return Normalize::subscription($this->fakeSubscription($accessCode));
    }

    public function studentDetailed(string $accessCode): array
    {
        return Normalize::subscription($this->fakeSubscription($accessCode));
    }

    public function token(): string
    {
        return 'fake-token';
    }

    public function forgetToken(): void {}

    /**
     * @return array<string, mixed>
     */
    private function fakeCourse(string $id, string $license, string $locale, string $title): array
    {
        return [
            'id' => $id,
            'license' => $license,
            'locale' => $locale,
            'title' => $title,
            'description' => null,
            'image' => null,
            'publishedAt' => date(DATE_ATOM),
            'updatedAt' => date(DATE_ATOM),
            'chapters' => [],
            'exams' => [],
            'theoryLessons' => [],
            'offer' => [
                'originalPrice' => ['amount' => '9.70', 'currency' => 'EUR'],
                'currentPrice' => ['amount' => '9.70', 'currency' => 'EUR'],
                'currentPriceDetails' => null,
                'vat' => 0.21,
                'suggestedRetailPrice' => ['amount' => '39.95', 'currency' => 'EUR'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fakeSubscription(string $accessCode): array
    {
        return [
            'id' => 'fake-subscription-'.substr(hash('sha256', $accessCode), 0, 8),
            'accessCode' => $accessCode,
            'createdAt' => date(DATE_ATOM),
            'expiresAt' => null,
            'activatedAt' => null,
            'lastSeenAt' => null,
            'course' => $this->courses[0],
            'courseCompletedAt' => null,
            'refresherCourseActivatedAt' => null,
            'blockedAt' => null,
            'blockedReason' => null,
            'progress' => 0,
            'progressUrl' => null,
            'progression' => [
                'chapters' => ['total' => 0, 'completed' => 0, 'progress' => 0, 'chapters' => []],
                'exams' => ['total' => 0, 'completed' => 0, 'progress' => 0, 'exams' => []],
                'theoryLessons' => ['total' => 0, 'attended' => 0, 'progress' => 0, 'reservations' => []],
            ],
        ];
    }
}
