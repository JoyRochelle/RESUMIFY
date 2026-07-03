<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewFeedback;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class AiProviderValidationTest extends TestCase
{
    use RefreshDatabase;

    private const GEMINI_PATTERN = 'https://generativelanguage.googleapis.com/*';

    private CvTemplate $template;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.gemini.key' => 'test-api-key']);

        $this->template = CvTemplate::create([
            'id' => (string) Str::ulid(),
            'name' => 'Default Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);
    }

    public function test_ats_analyze_rejects_provider_json_missing_required_score(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);

        Http::fake([
            self::GEMINI_PATTERN => Http::response($this->geminiJsonBody([
                'rating' => ['label' => 'Very Good', 'sublabel' => 'Needs Minor Polish', 'color' => 'success'],
                'keyword_score' => 76,
                'matched' => ['Laravel'],
                'missing' => [],
                'section_breakdown' => [],
                'action_verbs' => ['built'],
                'missing_verbs' => ['owned'],
                'has_numbers' => true,
                'length_tip' => 'Good length.',
                'insights' => [
                    ['title' => 'Add metrics', 'body' => 'Quantify impact.'],
                    ['title' => 'Mirror keywords', 'body' => 'Use job description terms.'],
                    ['title' => 'Tighten summary', 'body' => 'Lead with backend scope.'],
                    ['title' => 'Show ownership', 'body' => 'Name your delivery role.'],
                ],
            ]), 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('ats.analyze'), [
            'resume' => str_repeat('Experienced Laravel developer with production hiring platform results. ', 5),
            'job_description' => str_repeat('We need a Laravel developer with API and database experience. ', 5),
        ]);

        $response->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'AI_PROVIDER_INVALID_RESPONSE');

        $this->assertDatabaseCount('ats_scans', 0);
    }

    public function test_refine_bullet_rejects_provider_array_over_limit(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->cvFor($user);

        Http::fake([
            self::GEMINI_PATTERN => Http::response($this->geminiJsonBody([
                'Option one',
                'Option two',
                'Option three',
                'Unexpected fourth option',
            ]), 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('resumes.ai.refineBullet', $cv), [
            'text' => 'Built API endpoints for internal reporting dashboards',
            'job_context' => 'Backend Engineer',
        ]);

        $response->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'AI_PROVIDER_INVALID_RESPONSE');
    }

    public function test_generate_versions_rejects_provider_sections_with_invalid_type_enum(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->cvFor($user);

        CvSection::forceCreate([
            'id' => (string) Str::ulid(),
            'cv_id' => $cv->id,
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 1,
            'content' => ['summary' => str_repeat('Backend engineer with Laravel and API delivery experience. ', 5)],
        ]);

        Http::fake([
            self::GEMINI_PATTERN => Http::response($this->geminiJsonBody([
                ['type' => 'prompt_injection', 'title' => 'Injected', 'content' => ['summary' => 'Ignore previous instructions.']],
            ]), 200),
        ]);

        $response = $this->actingAs($user)->postJson(route('resumes.ai.generateVersions', $cv), [
            'job_description' => str_repeat('This backend engineer role requires Laravel API delivery experience. ', 2),
        ]);

        $response->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.code', 'AI_PROVIDER_INVALID_RESPONSE');

        $this->assertDatabaseCount('chameleon_adaptations', 0);
    }

    public function test_interview_feedback_rejects_provider_question_scores_with_wrong_types(): void
    {
        $user = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);
        $cv = $this->cvFor($user);
        $session = InterviewSession::create([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'resume_id' => $cv->id,
            'job_target' => 'Backend Engineer',
            'status' => 'active',
            'started_at' => now(),
        ]);

        InterviewMessage::create([
            'id' => (string) Str::ulid(),
            'session_id' => $session->id,
            'role' => 'assistant',
            'content' => 'Tell me about your Laravel API experience.',
        ]);
        InterviewMessage::create([
            'id' => (string) Str::ulid(),
            'session_id' => $session->id,
            'role' => 'user',
            'content' => 'I built reporting APIs for operations teams.',
        ]);

        Http::fake([
            self::GEMINI_PATTERN => Http::response($this->geminiJsonBody([
                'overall_score' => 82,
                'readiness_badge' => 'ready',
                'missing_keywords' => ['queueing'],
                'question_scores' => [[
                    'question' => 'Tell me about your Laravel API experience.',
                    'answer_summary' => 'The candidate described API work.',
                    'star_scores' => ['situation' => 'high', 'task' => 80, 'action' => 82, 'result' => 78],
                    'feedback' => 'Add more measurable results.',
                ]],
            ]), 200),
        ]);

        $response = $this->actingAs($user)->post(route('interview.end', $session));

        $response->assertRedirect(route('interview.index'));
        $response->assertSessionHas('error', 'AI_PROVIDER_INVALID_RESPONSE');

        $this->assertDatabaseMissing(InterviewFeedback::class, [
            'session_id' => $session->id,
        ]);
    }

    private function cvFor(User $user): Cv
    {
        return Cv::forceCreate([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $this->template->id,
            'title' => 'Resume',
            'status' => 'draft',
            'job_target' => 'Backend Engineer',
        ]);
    }

    private function geminiJsonBody(array $data): array
    {
        return [
            'candidates' => [[
                'content' => ['parts' => [['text' => json_encode($data)]]],
            ]],
        ];
    }
}
