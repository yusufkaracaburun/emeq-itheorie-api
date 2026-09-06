<?php

declare(strict_types=1);

namespace Emeq\ItheorieApi\Support;

final class Normalize
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function purchase(array $data): array
    {
        return [
            'id' => $data['id'] ?? null,
            'invoice' => $data['invoice'] ?? null,
            'created_at' => $data['createdAt'] ?? $data['created_at'] ?? null,
            'price' => $data['price'] ?? null,
            'subscription' => $data['subscription'] ?? null,
            'access_code' => $data['accessCode'] ?? $data['access_code'] ?? null,
            'expires_at' => $data['expiresAt'] ?? $data['expires_at'] ?? null,
            'login_url' => $data['loginUrl'] ?? $data['login_url'] ?? null,
            'direct_login_url' => $data['directLoginUrl'] ?? $data['direct_login_url'] ?? null,
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'mobile_phone_number' => $data['mobilePhoneNumber'] ?? $data['mobile_phone_number'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function course(array $data): array
    {
        $offer = $data['offer'] ?? null;

        return [
            'id' => $data['id'] ?? null,
            'license' => $data['license'] ?? null,
            'locale' => $data['locale'] ?? null,
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'published_at' => $data['publishedAt'] ?? null,
            'updated_at' => $data['updatedAt'] ?? null,
            'chapters' => $data['chapters'] ?? [],
            'exams' => $data['exams'] ?? [],
            'theory_lessons' => $data['theoryLessons'] ?? [],
            'offer' => is_array($offer) ? self::offer($offer) : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function offer(array $data): array
    {
        return [
            'original_price' => self::amount($data['originalPrice'] ?? null),
            'current_price' => self::amount($data['currentPrice'] ?? null),
            'current_price_details' => $data['currentPriceDetails'] ?? null,
            'vat' => $data['vat'] ?? 0,
            'suggested_retail_price' => self::amount($data['suggestedRetailPrice'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function money(array $data): array
    {
        return [
            'amount' => isset($data['amount']) ? (float) $data['amount'] : 0.0,
            'currency' => $data['currency'] ?? 'EUR',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function subscription(array $data): array
    {
        $course = $data['course'] ?? null;
        $progression = $data['progression'] ?? null;

        return [
            'id' => $data['id'] ?? null,
            'access_code' => $data['accessCode'] ?? null,
            'created_at' => $data['createdAt'] ?? null,
            'expires_at' => $data['expiresAt'] ?? null,
            'activated_at' => $data['activatedAt'] ?? null,
            'last_seen_at' => $data['lastSeenAt'] ?? null,
            'course' => is_array($course) ? self::course($course) : null,
            'course_completed_at' => $data['courseCompletedAt'] ?? null,
            'refresher_course_activated_at' => $data['refresherCourseActivatedAt'] ?? null,
            'blocked_at' => $data['blockedAt'] ?? null,
            'blocked_reason' => $data['blockedReason'] ?? null,
            'progress' => $data['progress'] ?? null,
            'progress_url' => $data['progressUrl'] ?? null,
            'progression' => is_array($progression) ? self::progression($progression) : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function progression(array $data): array
    {
        $chapters = $data['chapters'] ?? null;
        $exams = $data['exams'] ?? null;
        $theoryLessons = $data['theoryLessons'] ?? null;

        return [
            'chapters' => is_array($chapters) ? [
                'total' => $chapters['total'] ?? null,
                'completed' => $chapters['completed'] ?? null,
                'progress' => $chapters['progress'] ?? null,
                'chapters' => $chapters['chapters'] ?? [],
            ] : null,
            'exams' => is_array($exams) ? [
                'total' => $exams['total'] ?? null,
                'completed' => $exams['completed'] ?? null,
                'progress' => $exams['progress'] ?? null,
                'exams' => $exams['exams'] ?? [],
            ] : null,
            'theory_lessons' => is_array($theoryLessons) ? [
                'total' => $theoryLessons['total'] ?? null,
                'attended' => $theoryLessons['attended'] ?? null,
                'progress' => $theoryLessons['progress'] ?? null,
                'reservations' => $theoryLessons['reservations'] ?? [],
            ] : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  callable(array<string, mixed>): array<string, mixed>  $each
     * @return array{links: array<string, string|null>, data: list<array<string, mixed>>}
     */
    public static function collection(array $data, callable $each): array
    {
        $items = [];

        foreach ($data['data'] ?? [] as $item) {
            if (is_array($item)) {
                $items[] = $each($item);
            }
        }

        return [
            'links' => [
                'first' => $data['links']['first'] ?? null,
                'previous' => $data['links']['previous'] ?? null,
                'self' => $data['links']['self'] ?? null,
                'next' => $data['links']['next'] ?? null,
                'last' => $data['links']['last'] ?? null,
            ],
            'data' => $items,
        ];
    }

    private static function amount(mixed $money): float
    {
        return is_array($money) ? self::money($money)['amount'] : 0.0;
    }
}
