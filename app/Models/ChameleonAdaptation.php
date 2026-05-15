<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChameleonAdaptation extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';
    
    // Disable updated_at since migration only has created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'cv_id',
        'target_company',
        'tone_style',
        'adapted_content',
        'ai_prompt_used',
    ];

    protected function casts(): array
    {
        return [
            'adapted_content' => 'array',
        ];
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }
}
