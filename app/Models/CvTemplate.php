<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\Storage;

class CvTemplate extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'blade_path',
        'category',
        'description',
        'badge',
        'badge_color',
        'sort_order',
        'style_config',
        'thumbnail_url',
        'is_premium',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
            'style_config' => 'array',
        ];
    }

    // Accessor: thumbnail URL
    public function getThumbnailAttribute(): string
    {
        return $this->thumbnail_url
            ? Storage::disk('public')->url($this->thumbnail_url)
            : asset('images/template-placeholder.png');
    }

    // Helper: render this template with a CV's data
    public function renderHtml(Cv $cv): string
    {
        return view($this->blade_path, compact('cv'))->render();
    }
}
