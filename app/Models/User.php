<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AtsScan;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUlids, Notifiable, SoftDeletes;

    /**
     * Indicates that the IDs are not auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'ai_quota_reset_at'  => 'datetime',
            'password'           => 'hashed',
            'is_suspended'       => 'boolean',
        ];
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a customer (basic or premium).
     */
    public function isCustomer(): bool
    {
        return in_array($this->role, ['basic', 'premium']);
    }

    /**
     * Check if the user has a premium subscription.
     */
    public function isPremium(): bool
    {
        return $this->role === 'premium';
    }

    /**
     * Check if the user is on the Basic plan.
     */
    public function isBasic(): bool
    {
        return $this->role === 'basic';
    }

    /**
     * Get the maximum number of active resumes allowed for the user's plan.
     */
    public function getResumeLimit(): ?int
    {
        return config('plans.resume_limits.' . $this->role);
    }

    /**
     * Count active resumes for quota display and enforcement.
     */
    public function getResumeQuotaUsed(): int
    {
        return $this->cvs()->count();
    }

    /**
     * Check if the user can create another resume.
     */
    public function canCreateResume(): bool
    {
        $limit = $this->getResumeLimit();

        return $limit === null || $this->getResumeQuotaUsed() < $limit;
    }

    /**
     * Check if the user can use a named Premium feature.
     */
    public function canUsePremiumFeature(string $feature): bool
    {
        if ($this->isPremium() || $this->isAdmin()) {
            return true;
        }

        return !in_array($feature, config('plans.premium_features', []), true);
    }

    /**
     * Check if the user can use a Premium feature that Basic may still try a
     * limited number of times before the upgrade wall goes up.
     */
    public function canUseTrialFeature(string $feature): bool
    {
        return $this->canUsePremiumFeature($feature) || $this->hasTrialRemaining($feature);
    }

    /**
     * How many free runs of a trial feature the plan allows.
     */
    public function getTrialLimit(string $feature): int
    {
        return (int) config("plans.trials.{$feature}", 0);
    }

    /**
     * How many trial runs the user has already spent. Counted from records the
     * user has no way to delete, so clearing history cannot reset the trial.
     */
    public function getTrialUsed(string $feature): int
    {
        return match ($feature) {
            'ats_analyze' => $this->aiUsageLogs()->where('action_type', 'ats_analyze')->count(),
            'interview' => $this->interviewSessions()->count(),
            default => 0,
        };
    }

    /**
     * Trial runs left, or null when the plan is not trial-limited at all.
     */
    public function getTrialRemaining(string $feature): ?int
    {
        // Role, not the premium_features list: mock interview is trial-gated
        // without ever being listed as a Premium-only feature.
        if ($this->isPremium() || $this->isAdmin()) {
            return null;
        }

        return max(0, $this->getTrialLimit($feature) - $this->getTrialUsed($feature));
    }

    public function hasTrialRemaining(string $feature): bool
    {
        $remaining = $this->getTrialRemaining($feature);

        return $remaining === null || $remaining > 0;
    }

    /**
     * Prevent admin from receiving password reset emails.
     */
    public function sendPasswordResetNotification($token)
    {
        if ($this->role === 'admin') {
            return;
        }

        parent::sendPasswordResetNotification($token);
    }

    /**
     * Get the OAuth providers linked to this user.
     */
    public function oauthProviders(): HasMany
    {
        return $this->hasMany(OauthProvider::class);
    }


    /**
     * Get the resumes (CVs) owned by this user.
     */
    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class);
    }

    /**
     * Get the full URL for the user's avatar.
     */
    public function getAvatarUrlAttribute($value)
    {
        if ($value) {
            return str_starts_with($value, 'http') ? $value : asset('storage/' . $value);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    // Returns the max quota for this user based on their role
    public function getQuotaLimit(): int
    {
        return config('quota.' . $this->role, config('quota.basic'));
    }

    // Returns remaining quota this month
    public function getQuotaRemaining(): int
    {
        return max(0, $this->getQuotaLimit() - $this->ai_quota_used);
    }

    // Returns percentage used (for progress bar)
    public function getQuotaPercentage(): float
    {
        $limit = $this->getQuotaLimit();
        return $limit > 0 ? round(($this->ai_quota_used / $limit) * 100, 1) : 0;
    }

    /**
     * Check if the user has enough AI credits remaining.
     */
    public function hasQuotaRemaining(int $credits = 1): bool
    {
        return $this->getQuotaRemaining() >= $credits;
    }

    /**
     * Snapshot of this user's current AI quota, for JSON responses that
     * need the frontend to refresh a quota widget without a page reload.
     */
    public function aiQuotaSnapshot(): array
    {
        return [
            'used' => (int) $this->ai_quota_used,
            'limit' => $this->getQuotaLimit(),
            'remaining' => $this->getQuotaRemaining(),
            'percentage' => $this->getQuotaPercentage(),
        ];
    }

    /**
     * Get the AI usage logs for this user.
     */
    public function aiUsageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function adminLogs(): HasMany
    {
        return $this->hasMany(AdminLog::class, 'admin_id');
    }

    public function interviewSessions(): HasMany
    {
        return $this->hasMany(InterviewSession::class);
    }

    /**
     * Get the ATS scans run by this user, newest first.
     */
    public function atsScans(): HasMany
    {
        return $this->hasMany(AtsScan::class)->latest('created_at');
    }

}
