<?php

namespace Tests\Unit;

use App\Models\CvTemplate;
use App\Services\TemplateThumbnailService;
use Tests\TestCase;

class TemplateThumbnailServiceTest extends TestCase
{
    public function test_returns_existing_thumbnail_without_regenerating(): void
    {
        $template = new CvTemplate([
            'thumbnail_url' => 'templates/previews/existing.png',
        ]);

        $path = app(TemplateThumbnailService::class)->generate($template);

        $this->assertSame('templates/previews/existing.png', $path);
    }
}
