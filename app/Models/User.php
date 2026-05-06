<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUlids, Notifiable;

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
        'role',
        'avatar_url',
        'ai_quota_used',
        'ai_quota_reset_at',
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
        return $value ? asset('storage/' . $value) : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
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

    // relation 
    public function subscription():HasOne
    {
        return $this->hasOne(Subscription::class);
    }
}   
