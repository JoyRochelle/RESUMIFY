<?php

namespace App\Services;

class AiCreditReservation
{
    private function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly int $credits,
        public readonly int $previousUsage,
        public readonly int $usageAfterReservation,
        public readonly int $quotaLimit,
        public readonly string $status,
        public readonly ?string $context,
        private bool $refunded = false,
    ) {}

    public static function makeReserved(
        string $id,
        string $userId,
        int $credits,
        int $previousUsage,
        int $usageAfterReservation,
        int $quotaLimit,
        ?string $context = null,
        bool $refunded = false,
    ): self {
        return self::make(
            id: $id,
            userId: $userId,
            credits: $credits,
            previousUsage: $previousUsage,
            usageAfterReservation: $usageAfterReservation,
            quotaLimit: $quotaLimit,
            status: 'reserved',
            context: $context,
            refunded: $refunded,
        );
    }

    public static function makeBypassed(
        string $id,
        string $userId,
        int $credits,
        int $currentUsage,
        int $quotaLimit,
        ?string $context = null,
    ): self {
        return self::make(
            id: $id,
            userId: $userId,
            credits: $credits,
            previousUsage: $currentUsage,
            usageAfterReservation: $currentUsage,
            quotaLimit: $quotaLimit,
            status: 'bypassed',
            context: $context,
        );
    }

    public static function makeDenied(
        string $id,
        string $userId,
        int $credits,
        int $currentUsage,
        int $quotaLimit,
        ?string $context = null,
    ): self {
        return self::make(
            id: $id,
            userId: $userId,
            credits: $credits,
            previousUsage: $currentUsage,
            usageAfterReservation: $currentUsage,
            quotaLimit: $quotaLimit,
            status: 'denied',
            context: $context,
        );
    }

    private static function make(
        string $id,
        string $userId,
        int $credits,
        int $previousUsage,
        int $usageAfterReservation,
        int $quotaLimit,
        string $status,
        ?string $context,
        bool $refunded = false,
    ): self {
        if ($credits < 1) {
            throw new \InvalidArgumentException('AI credit reservation must require at least one credit.');
        }

        if (! in_array($status, ['reserved', 'bypassed', 'denied'], true)) {
            throw new \InvalidArgumentException('Invalid AI credit reservation status.');
        }

        if ($previousUsage < 0 || $usageAfterReservation < 0 || $quotaLimit < 0) {
            throw new \InvalidArgumentException('AI credit reservation usage values must be non-negative.');
        }

        return new self(
            id: $id,
            userId: $userId,
            credits: $credits,
            previousUsage: $previousUsage,
            usageAfterReservation: $usageAfterReservation,
            quotaLimit: $quotaLimit,
            status: $status,
            context: $context,
            refunded: $refunded,
        );
    }

    public function isReserved(): bool
    {
        return $this->status === 'reserved';
    }

    public function reserved(): bool
    {
        return $this->isReserved();
    }

    public function isBypassed(): bool
    {
        return $this->status === 'bypassed';
    }

    public function bypassed(): bool
    {
        return $this->isBypassed();
    }

    public function isDenied(): bool
    {
        return $this->status === 'denied';
    }

    public function remainingCredits(): int
    {
        return max(0, $this->quotaLimit - $this->usageAfterReservation);
    }

    public function wasRefunded(): bool
    {
        return $this->refunded;
    }

    public function markRefunded(): void
    {
        $this->refunded = true;
    }
}
