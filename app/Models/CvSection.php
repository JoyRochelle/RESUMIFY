<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CvSection extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cv_id',
        'type',
        'title',
        'content',
        'order',
        'last_saved_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    protected function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
