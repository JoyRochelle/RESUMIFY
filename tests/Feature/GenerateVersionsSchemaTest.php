<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Regression coverage for a real bug reproduced against the live Gemini API:
 * cvVersionsSchema()'s polymorphic `content` field made Gemini reject the
 * request outright — first with "Unknown name additionalProperties", and
 * after that was stripped, with "too many states for serving" (its
 * constrained-decoding grammar limit). The fix drops response_schema for
 * this call entirely and relies on CvVersionsResponse::fromProvider for
 * validation, same as before.
 */
class GenerateVersionsSchemaTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_PATTERN = 'https://generativelanguage.googleapis.com/*';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.gemini.key' => 'test-api-key']);

        CvTemplate::create([
            'id' => (string) Str::ulid(),
            'name' => 'Default Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);
    }

    protected function createCvForUser(User $user): Cv
    {
        $cv = Cv::forceCreate([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'template_id' => CvTemplate::first()->id,
            'title' => 'My Test Resume',
            'status' => 'draft',
        ]);

        CvSection::forceCreate([
            'id' => (string) Str::ulid(),
            'cv_id' => $cv->id,
            'type' => 'skills',
            'title' => 'Skills',
            'order' => 1,
            'content' => ['PHP', 'Laravel', str_repeat('Long enough resume content. ', 10)],
        ]);

        return $cv;
    }

    private function geminiJsonBody(array $data): array
    {
        return [
            'candidates' => [[
                'content' => ['parts' => [['text' => json_encode($data)]]],
            ]],
        ];
    }

    public function test_generate_versions_request_omits_response_schema(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        Http::fake(function (Request $request) {
            $config = $request->data()['generationConfig'] ?? [];

            $this->assertSame('application/json', $config['response_mime_type'] ?? null);
            $this->assertArrayNotHasKey('response_schema', $config, 'Gemini rejects this schema shape — response_schema must be omitted for generate-versions.');

            return Http::response($this->geminiJsonBody([
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel']],
            ]), 200);
        });

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a detailed job description intended to exceed fifty characters. ', 2),
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_generate_versions_succeeds_without_response_schema_constraint(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->createCvForUser($user);

        Http::fake([
            self::GEMINI_PATTERN => Http::response($this->geminiJsonBody([
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel', 'MySQL']],
            ]), 200),
        ]);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/generate-versions", [
            'job_description' => str_repeat('This is a detailed job description intended to exceed fifty characters. ', 2),
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseCount('chameleon_adaptations', 3);
    }
}
