<?php

namespace Tests\Feature;

use App\Models\CvTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TemplateThumbnailDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_thumbnail_is_served_through_the_application_route(): void
    {
        Storage::fake('public');
        config(['app.url' => '<ISI_URL_RENDER_KAMU>']);

        $template = CvTemplate::factory()->create([
            'thumbnail_url' => 'templates/previews/example.png',
        ]);

        Storage::disk('public')->put($template->thumbnail_url, 'cached-thumbnail');

        $this->assertSame(
            "/templates/{$template->id}/thumbnail",
            $template->thumbnail
        );

        $this->get($template->thumbnail)
            ->assertOk()
            ->assertHeader('content-type', 'image/png')
            ->assertHeader('cache-control', 'max-age=86400, public, stale-while-revalidate=604800')
            ->assertStreamedContent('cached-thumbnail');
    }

    public function test_missing_local_thumbnail_returns_not_found(): void
    {
        Storage::fake('public');

        $template = CvTemplate::factory()->create([
            'thumbnail_url' => 'templates/previews/missing.png',
        ]);

        $this->get("/templates/{$template->id}/thumbnail")
            ->assertNotFound();
    }
}
