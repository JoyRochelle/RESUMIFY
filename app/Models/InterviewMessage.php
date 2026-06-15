<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewMessage extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'session_id',
        'role',
        'content',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(InterviewSession::class, 'session_id');
    }
}
