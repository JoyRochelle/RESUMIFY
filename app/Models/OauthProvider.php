<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class OauthProvider extends Model
{
    use HasUlids;

    public $incrementing = false;
    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
        ];
    }

    public function updateToken(?string $token): bool
    {
        if (! $this->exists) {
            $this->token = $token;

            return $this->save();
        }

        $encryptedToken = $token === null ? null : Crypt::encryptString($token);

        DB::table($this->getTable())
            ->where($this->getKeyName(), $this->getKey())
            ->update(['token' => $encryptedToken]);

        $this->setRawAttributes(array_replace($this->getAttributes(), [
            'token' => $encryptedToken,
        ]), true);

        return true;
    }
}
