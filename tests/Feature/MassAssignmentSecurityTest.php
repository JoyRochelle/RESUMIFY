<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MassAssignmentSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_facing_payload_cannot_mass_assign_user_privilege_or_quota_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'basic',
            'is_suspended' => false,
            'ai_quota_used' => 1,
            'ai_quota_reset_at' => now()->subDay(),
        ]);

        $user->fill([
            'name' => 'Updated Name',
            'role' => 'admin',
            'is_suspended' => true,
            'ai_quota_used' => 999,
            'ai_quota_reset_at' => now()->addYear(),
        ])->save();

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('basic', $user->role);
        $this->assertFalse((bool) $user->is_suspended);
        $this->assertSame(1, $user->ai_quota_used);
        $this->assertTrue($user->ai_quota_reset_at->isPast());
    }

    public function test_user_facing_payload_cannot_mass_assign_cv_owner_status_or_score_fields(): void
    {
        $owner = User::factory()->create(['role' => 'basic']);
        $attacker = User::factory()->create(['role' => 'basic']);
        $template = CvTemplate::create([
            'name' => 'Default Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);

        $cv = Cv::forceCreate([
            'user_id' => $owner->id,
            'template_id' => $template->id,
            'title' => 'Original Resume',
            'status' => 'draft',
            'ats_score' => 12,
        ]);

        $cv->fill([
            'title' => 'Updated Resume',
            'user_id' => $attacker->id,
            'status' => 'completed',
            'ats_score' => 100,
        ])->save();

        $cv->refresh();

        $this->assertSame('Updated Resume', $cv->title);
        $this->assertSame($owner->id, $cv->user_id);
        $this->assertSame('draft', $cv->status);
        $this->assertSame(12, $cv->ats_score);
    }

    public function test_user_facing_payload_cannot_mass_assign_cv_section_owner_order_or_save_metadata(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $template = CvTemplate::create([
            'name' => 'Default Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);

        $resume = Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Primary Resume',
            'status' => 'draft',
        ]);
        $otherResume = Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Other Resume',
            'status' => 'draft',
        ]);
        $section = CvSection::forceCreate([
            'cv_id' => $resume->id,
            'type' => 'skills',
            'title' => 'Skills',
            'content' => ['items' => ['Laravel']],
            'order' => 2,
            'last_saved_at' => now()->subDay(),
        ]);

        $section->fill([
            'title' => 'Updated Skills',
            'cv_id' => $otherResume->id,
            'order' => 99,
            'last_saved_at' => now()->addYear(),
        ])->save();

        $section->refresh();

        $this->assertSame('Updated Skills', $section->title);
        $this->assertSame($resume->id, $section->cv_id);
        $this->assertSame(2, $section->order);
        $this->assertTrue($section->last_saved_at->isPast());
    }
}
