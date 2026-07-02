<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FreemiumPlanTest extends TestCase
{
    use RefreshDatabase;

    private function template(array $attributes = []): CvTemplate
    {
        return CvTemplate::create(array_merge([
            'id' => (string) Str::ulid(),
            'name' => 'Foundation',
            'blade_path' => 'templates.foundation',
            'category' => 'professional',
            'description' => 'Clean ATS-safe template.',
            'is_active' => true,
            'is_premium' => false,
            'sort_order' => 1,
        ], $attributes));
    }

    private function cv(User $user, CvTemplate $template, array $attributes = []): Cv
    {
        $cv = Cv::create(array_merge([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Product Resume',
            'status' => 'draft',
        ], $attributes));

        CvSection::create([
            'id' => (string) Str::ulid(),
            'cv_id' => $cv->id,
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 1,
            'content' => [
                'name' => 'Jane Doe',
                'title' => 'Product Designer',
                'email' => 'jane@example.com',
                'summary' => 'Experienced product designer with measurable outcomes across hiring platforms.',
            ],
        ]);

        return $cv;
    }

    public function test_basic_user_sees_premium_templates_as_locked_on_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $this->template();
        $this->template([
            'name' => 'Boardroom Premium',
            'is_premium' => true,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Boardroom Premium');
        $response->assertSee('Locked Premium Template');
        $response->assertSee('Basic Member');
        $response->assertSee('0/1 Resumes Created');
    }

    public function test_basic_user_cannot_create_resume_with_premium_template(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $premiumTemplate = $this->template(['is_premium' => true]);

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'Premium Attempt',
            'template_id' => $premiumTemplate->id,
        ]);

        $response->assertRedirect(route('user.upgrade-quota'));
        $this->assertDatabaseMissing('cvs', [
            'user_id' => $user->id,
            'title' => 'Premium Attempt',
        ]);
    }

    public function test_basic_user_cannot_create_second_resume(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $template = $this->template();
        $this->cv($user, $template);

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'Second Resume',
            'template_id' => $template->id,
        ]);

        $response->assertRedirect(route('user.upgrade-quota'));
        $this->assertDatabaseMissing('cvs', [
            'user_id' => $user->id,
            'title' => 'Second Resume',
        ]);
    }

    public function test_premium_user_can_create_more_than_one_resume(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $template = $this->template();
        $this->cv($user, $template);

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'Second Premium Resume',
            'template_id' => $template->id,
        ]);

        $newCv = Cv::where('title', 'Second Premium Resume')->first();
        $this->assertNotNull($newCv);
        $response->assertRedirect(route('user.manuscript', ['cv_id' => $newCv->id]));
    }

    public function test_basic_user_gets_402_for_premium_ats_analyze(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);

        $response = $this->actingAs($user)->postJson(route('ats.analyze'), [
            'resume' => str_repeat('resume content with achievements ', 4),
            'job_description' => str_repeat('job description with product metrics ', 4),
        ]);

        $response->assertStatus(402)
            ->assertJson([
                'error' => 'premium_required',
                'upgrade_url' => route('user.upgrade-quota'),
            ]);
    }

    public function test_basic_user_gets_402_for_pdf_export(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $template = $this->template();
        $cv = $this->cv($user, $template);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('resumes.pdf', $cv));

        $response->assertStatus(402)
            ->assertJson([
                'error' => 'premium_required',
                'upgrade_url' => route('user.upgrade-quota'),
            ]);
    }

    public function test_premium_user_can_export_pdf(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $template = $this->template();
        $cv = $this->cv($user, $template);

        $response = $this->actingAs($user)->get(route('resumes.pdf', $cv));

        $response->assertOk();
    }
}
