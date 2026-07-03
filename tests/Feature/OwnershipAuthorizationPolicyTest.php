<?php

namespace Tests\Feature;

use App\Models\AtsScan;
use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\InterviewSession;
use App\Models\SupportTicket;
use App\Models\User;
use App\Policies\AtsScanPolicy;
use App\Policies\InterviewSessionPolicy;
use App\Policies\SupportTicketPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class OwnershipAuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_interview_session_policy_protects_cross_user_session_flows(): void
    {
        $owner = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $attacker = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->cvFor($owner);
        $session = InterviewSession::create([
            'id' => (string) Str::ulid(),
            'user_id' => $owner->id,
            'resume_id' => $cv->id,
            'job_target' => 'Backend Engineer',
            'status' => 'active',
            'started_at' => now(),
        ]);

        $this->assertTrue(
            class_exists(InterviewSessionPolicy::class),
            'Interview session ownership must be centralized in InterviewSessionPolicy.'
        );

        $this->actingAs($attacker)->get(route('interview.show', $session))->assertForbidden();
        $this->actingAs($attacker)->post(route('interview.message', $session), ['content' => 'Hello'])->assertForbidden();
        $this->actingAs($attacker)->post(route('interview.stream', $session), ['content' => 'Hello'])->assertForbidden();
        $this->actingAs($attacker)->post(route('interview.end', $session))->assertForbidden();
        $this->actingAs($attacker)->get(route('interview.feedback', $session))->assertForbidden();
    }

    public function test_ats_scan_policy_protects_cross_user_history_flows(): void
    {
        $owner = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $attacker = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
        $cv = $this->cvFor($owner);
        $scan = AtsScan::create([
            'user_id' => $owner->id,
            'cv_id' => $cv->id,
            'job_description' => 'Laravel backend engineer role.',
            'score' => 81,
            'matched_keywords' => ['Laravel'],
            'suggestions' => ['Add measurable outcomes.'],
            'result_json' => ['score' => 81],
        ]);

        $this->assertTrue(
            class_exists(AtsScanPolicy::class),
            'ATS scan ownership must be centralized in AtsScanPolicy.'
        );

        $this->actingAs($attacker)->getJson(route('ats.history.show', $scan))->assertForbidden();
        $this->actingAs($attacker)->deleteJson(route('ats.history.destroy', $scan))->assertForbidden();
        $this->assertDatabaseHas('ats_scans', ['id' => $scan->id]);
    }

    public function test_support_ticket_policy_protects_cross_user_ticket_access(): void
    {
        $owner = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $attacker = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $ticket = SupportTicket::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue(
            class_exists(SupportTicketPolicy::class),
            'Support ticket ownership must be centralized in SupportTicketPolicy.'
        );

        $this->actingAs($attacker)->get(route('help.tickets.show', $ticket))->assertForbidden();
    }

    public function test_notification_read_actions_are_scoped_to_authenticated_user_notifications(): void
    {
        $owner = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $attacker = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $ownersNotificationId = (string) Str::uuid();
        $attackersNotificationId = (string) Str::uuid();

        DB::table('notifications')->insert([
            [
                'id' => $ownersNotificationId,
                'type' => 'test-notification',
                'notifiable_type' => User::class,
                'notifiable_id' => $owner->id,
                'data' => json_encode(['message' => 'Owner only']),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => $attackersNotificationId,
                'type' => 'test-notification',
                'notifiable_type' => User::class,
                'notifiable_id' => $attacker->id,
                'data' => json_encode(['message' => 'Attacker only']),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($attacker)
            ->get(route('notifications.read', $ownersNotificationId))
            ->assertNotFound();

        $this->actingAs($attacker)
            ->from('/dashboard')
            ->post(route('notifications.readAll'))
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('notifications', [
            'id' => $ownersNotificationId,
            'read_at' => null,
        ]);
        $this->assertNotNull(DB::table('notifications')->where('id', $attackersNotificationId)->value('read_at'));
    }

    private function cvFor(User $user): Cv
    {
        $template = CvTemplate::create([
            'id' => (string) Str::ulid(),
            'name' => 'Ownership Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);

        return Cv::create([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Ownership CV',
            'status' => 'draft',
        ]);
    }
}
