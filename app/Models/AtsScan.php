<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtsScan extends Model
{
    use HasUlids;

    /**
     * This table only has created_at, no updated_at.
     */
    const UPDATED_AT = null;

    protected $table = 'ats_scans';

    protected $fillable = [
        'user_id',
        'cv_id',
        'job_title',
        'job_company',
        'job_description',
        'score',
        'matched_keywords',
        'suggestions',
        'result_json',
    ];

    protected function casts(): array
    {
        return [
            'score'            => 'integer',
            'matched_keywords' => 'array',
            'suggestions'      => 'array',
            'result_json'      => 'array',
            'created_at'       => 'datetime',
        ];
    }

    /**
     * The user who ran this scan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The CV used in this scan (nullable).
     */
    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
