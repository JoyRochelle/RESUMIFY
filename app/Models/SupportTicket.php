<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'user_id',
        'subject',
        'status',
        'assigned_to',
        'close_requested_by',
        'close_requested_at',
    ];

    protected function casts(): array
    {
        return [
            'close_requested_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function closeRequestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'close_requested_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    /**
     * The user on the opposite side of the conversation from $actor:
     * the assigned admin when $actor is the ticket owner, or the
     * ticket owner when $actor is an admin.
     */
    public function otherParty(User $actor): ?User
    {
        return $actor->id === $this->user_id ? $this->assignedAdmin : $this->user;
    }

    public function requestClose(User $actor): void
    {
        $this->update([
            'status' => 'awaiting_closure',
            'close_requested_by' => $actor->id,
            'close_requested_at' => now(),
        ]);
    }

    public function confirmClose(): void
    {
        $this->update([
            'status' => 'closed',
            'close_requested_by' => null,
            'close_requested_at' => null,
        ]);
    }

    public function rejectClose(): void
    {
        $this->update([
            'status' => 'open',
            'close_requested_by' => null,
            'close_requested_at' => null,
        ]);
    }
}
