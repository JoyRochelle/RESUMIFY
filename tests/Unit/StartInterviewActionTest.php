<?php

namespace Tests\Unit;

use App\Actions\Interviews\StartInterviewAction;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\User;
use App\Services\InterviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartInterviewActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_starts_an_interview_consumes_quota_and_logs_usage(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);
        $template = CvTemplate::factory()->create();
        $resume = Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Backend Resume',
            'job_target' => 'Backend Engineer',
        ]);

        $interviewService = $this->createMock(InterviewService::class);
        $interviewService->expects($this->once())
            ->method('startSession')
            ->with($user, $resume, 'Senior Laravel Engineer')
            ->willReturnCallback(fn () => [
                'session' => $user->interviewSessions()->create([
                    'resume_id' => $resume->id,
                    'job_target' => 'Senior Laravel Engineer',
                    'status' => 'active',
                    'started_at' => now(),
                ]),
                'message' => 'Tell me about your Laravel experience.',
            ]);
        $this->app->instance(InterviewService::class, $interviewService);

        $result = app(StartInterviewAction::class)->execute($user, $resume, 'Senior Laravel Engineer');

        $this->assertSame('Tell me about your Laravel experience.', $result['message']);
        $this->assertSame($user->id, $result['session']->user_id);
        $this->assertSame(1, $user->refresh()->ai_quota_used);
        $this->assertDatabaseHas('ai_usage_logs', [
            'user_id' => $user->id,
            'action_type' => 'interview_question',
            'resume_id' => $resume->id,
        ]);
    }
}
