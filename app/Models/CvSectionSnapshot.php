<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvSectionSnapshot extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    const UPDATED_AT = null;

    protected $fillable = [
        'cv_id',
        'sections',
        'reason',
        'source_adaptation_id',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
        ];
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function sourceAdaptation(): BelongsTo
    {
        return $this->belongsTo(ChameleonAdaptation::class, 'source_adaptation_id');
    }
}
