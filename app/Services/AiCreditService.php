<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AiCreditService
{
    public function reserve(User $user, int $credits = 1, ?string $context = null): AiCreditReservation
    {
        if ($credits < 1) {
            throw new \InvalidArgumentException('AI credit reservation must require at least one credit.');
        }

        $context = $this->normalizeContext($context);

        return DB::transaction(function () use ($user, $credits, $context) {
            $lockedUser = $this->lockUser($user);
            $quotaLimit = $lockedUser->getQuotaLimit();
            $currentUsage = (int) $lockedUser->ai_quota_used;

            if ($lockedUser->isPremium() || $lockedUser->isAdmin()) {
                $reservationId = $this->recordReservation(
                    user: $lockedUser,
                    credits: $credits,
                    usageBefore: $currentUsage,
                    usageAfter: $currentUsage,
                    quotaLimit: $quotaLimit,
                    status: 'bypassed',
                    context: $context,
                );
                $this->syncUserSnapshot($user, $lockedUser);

                return AiCreditReservation::makeBypassed($reservationId, $lockedUser->id, $credits, $currentUsage, $quotaLimit, $context);
            }

            if (($quotaLimit - $currentUsage) < $credits) {
                $reservationId = $this->recordReservation(
                    user: $lockedUser,
                    credits: $credits,
                    usageBefore: $currentUsage,
                    usageAfter: $currentUsage,
                    quotaLimit: $quotaLimit,
                    status: 'denied',
                    context: $context,
                );
                $this->syncUserSnapshot($user, $lockedUser);

                return AiCreditReservation::makeDenied($reservationId, $lockedUser->id, $credits, $currentUsage, $quotaLimit, $context);
            }

            $newUsage = $currentUsage + $credits;

            $lockedUser->forceFill(['ai_quota_used' => $newUsage])->save();
            $reservationId = $this->recordReservation(
                user: $lockedUser,
                credits: $credits,
                usageBefore: $currentUsage,
                usageAfter: $newUsage,
                quotaLimit: $quotaLimit,
                status: 'reserved',
                context: $context,
            );
            $this->syncUserSnapshot($user, $lockedUser);

            return AiCreditReservation::makeReserved(
                id: $reservationId,
                userId: $lockedUser->id,
                credits: $credits,
                previousUsage: $currentUsage,
                usageAfterReservation: $newUsage,
                quotaLimit: $quotaLimit,
                context: $context,
            );
        });
    }

    public function refund(AiCreditReservation $reservation): AiCreditReservation
    {
        if (! $reservation->isReserved() || $reservation->wasRefunded()) {
            return $reservation;
        }

        DB::transaction(function () use ($reservation) {
            $reservationRecord = DB::table('ai_credit_reservations')
                ->where('id', $reservation->id)
                ->lockForUpdate()
                ->first();

            if (! $reservationRecord || $reservationRecord->refunded_at !== null) {
                $reservation->markRefunded();

                return;
            }

            $lockedUser = User::query()
                ->whereKey($reservation->userId)
                ->lockForUpdate()
                ->firstOrFail();

            $currentUsage = (int) $lockedUser->ai_quota_used;
            $newUsage = $currentUsage <= $reservation->previousUsage
                ? $currentUsage
                : max($reservation->previousUsage, $currentUsage - $reservation->credits);

            $lockedUser->forceFill(['ai_quota_used' => $newUsage])->save();
            DB::table('ai_credit_reservations')
                ->where('id', $reservation->id)
                ->update([
                    'refunded_at' => now(),
                    'updated_at' => now(),
                ]);
            $reservation->markRefunded();
        });

        return $reservation;
    }

    private function lockUser(User $user): User
    {
        if (! $user->exists || $user->getKey() === null) {
            throw new \InvalidArgumentException('AI credit reservation requires a persisted user.');
        }

        return User::query()
            ->whereKey($user->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function recordReservation(
        User $user,
        int $credits,
        int $usageBefore,
        int $usageAfter,
        int $quotaLimit,
        string $status,
        ?string $context,
    ): string {
        $reservationId = (string) Str::ulid();

        DB::table('ai_credit_reservations')->insert([
            'id' => $reservationId,
            'user_id' => $user->id,
            'credits' => $credits,
            'usage_before' => $usageBefore,
            'usage_after' => $usageAfter,
            'quota_limit' => $quotaLimit,
            'context' => $context,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $reservationId;
    }

    private function syncUserSnapshot(User $target, User $source): void
    {
        $target->setRawAttributes($source->getAttributes(), true);
    }

    private function normalizeContext(?string $context): ?string
    {
        if ($context === null || $context === '') {
            return null;
        }

        return mb_substr($context, 0, 100);
    }
}
