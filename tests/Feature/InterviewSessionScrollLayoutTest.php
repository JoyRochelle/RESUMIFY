<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The session page uses h-screen (100vh) for its root container. Mobile
 * browsers resize the *visual* viewport as their address bar shows/hides,
 * so a fixed 100vh block can end up taller than what's actually visible,
 * pushing the whole page into native body-level scroll instead of only
 * the #messages region — dragging the header, status pill, and input bar
 * along with it. h-dvh (dynamic viewport height) tracks the real visible
 * viewport and avoids this.
 */
class InterviewSessionScrollLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini.key' => 'test-api-key']);
    }

    private function makeUserWithCv(string $role = 'premium'): array
    {
        $template = CvTemplate::create([
            'id'         => Str::ulid(),
            'name'       => 'Test Template',
            'blade_path' => 'templates.default',
            'is_active'  => true,
            'is_premium' => false,
        ]);

        $user = User::factory()->create(['role' => $role, 'ai_quota_used' => 0]);

        $cv = Cv::forceCreate([
            'id'          => Str::ulid(),
            'user_id'     => $user->id,
            'template_id' => $template->id,
            'title'       => 'Test CV',
            'job_target'  => 'Backend Engineer',
        ]);

        CvSection::forceCreate([
            'id'      => Str::ulid(),
            'cv_id'   => $cv->id,
            'type'    => 'target_job',
            'title'   => 'Target Job',
            'order'   => 0,
            'content' => ['job_title' => 'Backend Engineer'],
        ]);

        return [$user, $cv];
    }

    private function makeSession(User $user, Cv $cv): InterviewSession
    {
        $session = InterviewSession::create([
            'id'         => Str::ulid(),
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => 'Backend Engineer',
            'status'     => 'active',
            'started_at' => now(),
        ]);

        InterviewMessage::create([
            'id'         => Str::ulid(),
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => 'Tell me about yourself.',
        ]);

        return $session;
    }

    public function test_root_container_uses_dynamic_viewport_height_not_static_100vh(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        $response = $this->actingAs($user)->get("/interview/sessions/{$session->id}");

        $response->assertStatus(200);
        $response->assertDontSee('overflow-hidden h-screen', false);
        $response->assertSee('overflow-hidden h-dvh', false);
    }

    public function test_header_and_input_bar_are_excluded_from_the_scrollable_region(): void
    {
        [$user, $cv] = $this->makeUserWithCv();
        $session     = $this->makeSession($user, $cv);

        $response = $this->actingAs($user)->get("/interview/sessions/{$session->id}");
        $html     = $response->getContent();

        // Header (with the End Session / Session Ended pill) and the input
        // bar must stay shrink-0 siblings of the scroll container, never
        // wrapped inside the overflow-y-auto #messages div.
        $messagesStart = strpos($html, 'id="messages"');
        $inputBarPos   = strpos($html, 'id="user-input"');

        $this->assertNotFalse($messagesStart);
        $this->assertNotFalse($inputBarPos);

        $messagesDivClose = strpos($html, '</div>', $messagesStart);
        $this->assertGreaterThan(
            $messagesDivClose,
            $inputBarPos,
            'The input bar must be rendered after the #messages div closes, not inside the scrollable region.'
        );
    }
}
