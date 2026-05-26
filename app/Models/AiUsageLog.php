<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    use HasUlids;

    /**
     * Logs are append-only — no updated_at column.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action_type',
        'tokens_used',
        'cost_usd',
        'resume_id',
    ];

    protected function casts(): array
    {
        return [
            'tokens_used' => 'integer',
            'cost_usd'    => 'decimal:6',
        ];
    }

    /**
     * The user who triggered this AI action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
