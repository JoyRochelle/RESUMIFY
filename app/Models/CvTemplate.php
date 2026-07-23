<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CvTemplate extends Model
{
    use HasFactory, HasUlids;

    /**
     * Allowlist pattern for blade_path. Templates may only ever resolve to a
     * view inside the `templates.*` namespace (resources/views/templates/*).
     * This prevents a tampered/injected blade_path from rendering arbitrary
     * application views (e.g. admin.*, emails.*) — a path-injection / SSRF-style
     * vulnerability, since the value flows straight into view().
     */
    public const BLADE_PATH_PATTERN = '/^templates\.[a-z0-9\-]+$/';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'blade_path',
        'category',
        'experience_level',
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
        if (! $this->thumbnail_url) {
            return asset('images/template-placeholder.png');
        }

        if (preg_match('/^https?:\/\//i', $this->thumbnail_url)) {
            return $this->thumbnail_url;
        }

        if (config('filesystems.disks.public.driver') === 'local') {
            return route('templates.thumbnail', $this, false);
        }

        return Storage::disk('public')->url($this->thumbnail_url);
    }

    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class, 'template_id');
    }

    /**
     * Return the blade_path only if it is a safe, existing CV template view.
     * Aborts with 404 otherwise so a malicious/tampered path can never be
     * rendered. Use this everywhere blade_path is passed to view().
     */
    public function safeBladePath(): string
    {
        $path = (string) $this->blade_path;

        if (! preg_match(self::BLADE_PATH_PATTERN, $path) || ! view()->exists($path)) {
            abort(404, 'Template not found.');
        }

        return $path;
    }

    // Helper: render this template with a CV's data
    public function renderHtml(Cv $cv): string
    {
        return view($this->safeBladePath(), compact('cv'))->render();
    }
}
