<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Data;

final readonly class PurchaseRequest
{
    public function __construct(
        public string $course,
        public string $name,
        public string $email,
        public ?string $mobilePhone = null,
        public ?bool $permissionToShareProgress = null,
    ) {}

    /**
     * @return array<string, scalar>
     */
    public function toArray(): array
    {
        $payload = [
            'course' => $this->course,
            'name' => $this->name,
            'email' => $this->email,
            'mobilePhone' => $this->mobilePhone,
            'permissionToShareProgress' => $this->permissionToShareProgress,
        ];

        return array_filter($payload, static fn (mixed $value): bool => $value !== null);
    }
}
