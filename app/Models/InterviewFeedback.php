<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewFeedback extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType   = 'string';
    public $timestamps   = false;

    protected $table = 'interview_feedback';

    protected $fillable = [
        'session_id',
        'question_scores',
        'missing_keywords',
        'overall_score',
        'readiness_badge',
    ];

    protected function casts(): array
    {
        return [
            'question_scores'  => 'array',
            'missing_keywords' => 'array',
            'overall_score'    => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(InterviewSession::class, 'session_id');
    }
}
