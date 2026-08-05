<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * PDF export used to sit in config('plans.premium_features'), so every Basic
 * account got a 402. It is free now; what stays paid is the premium template,
 * which a downgraded account can still be holding on an existing resume.
 */
class BasicPdfExportTest extends TestCase
{
    use RefreshDatabase;

    private function template(bool $premium = false): CvTemplate
    {
        return CvTemplate::create([
            'id' => (string) Str::ulid(),
            'name' => $premium ? 'Boardroom' : 'Foundation',
            'blade_path' => $premium ? 'templates.boardroom' : 'templates.foundation',
            'category' => 'professional',
            'description' => 'Test template.',
            'is_active' => true,
            'is_premium' => $premium,
            'sort_order' => 1,
        ]);
    }

    private function cv(User $user, CvTemplate $template): Cv
    {
        $cv = Cv::forceCreate([
            'id' => (string) Str::ulid(),
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Export Resume',
            'status' => 'draft',
        ]);

        CvSection::forceCreate([
            'id' => (string) Str::ulid(),
            'cv_id' => $cv->id,
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 1,
            'content' => [
                'name' => 'Jane Doe',
                'title' => 'Product Designer',
                'email' => 'jane@example.com',
            ],
        ]);

        return $cv->fresh('sections');
    }

    public function test_pdf_export_is_not_a_premium_feature_anymore(): void
    {
        $this->assertNotContains('pdf_export', config('plans.premium_features'));
    }

    public function test_basic_user_downloads_a_pdf_on_a_free_template(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->cv($user, $this->template());

        $response = $this->actingAs($user)->get(route('resumes.pdf', $cv));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_basic_user_is_blocked_on_a_premium_template(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->cv($user, $this->template(premium: true));

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('resumes.pdf', $cv));

        $response->assertStatus(402)
            ->assertJson([
                'error' => 'premium_template_required',
                'upgrade_url' => route('user.upgrade-quota'),
            ]);
    }

    public function test_blocked_basic_user_is_told_they_can_switch_template_instead(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->cv($user, $this->template(premium: true));

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('resumes.pdf', $cv));

        // A dead end would be wrong: this resume can be exported today by
        // moving it to a free template.
        $this->assertStringContainsString('free template', $response->json('message'));
    }

    public function test_browser_request_on_a_premium_template_redirects_with_an_error(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->cv($user, $this->template(premium: true));

        $this->actingAs($user)
            ->get(route('resumes.pdf', $cv))
            ->assertRedirect(route('user.upgrade-quota'))
            ->assertSessionHas('error');
    }

    public function test_premium_user_exports_a_premium_template(): void
    {
        $user = User::factory()->create(['role' => 'premium']);
        $cv = $this->cv($user, $this->template(premium: true));

        $this->actingAs($user)
            ->get(route('resumes.pdf', $cv))
            ->assertOk();
    }

    public function test_ownership_still_governs_export(): void
    {
        $owner = User::factory()->create(['role' => 'basic']);
        $stranger = User::factory()->create(['role' => 'premium']);
        $cv = $this->cv($owner, $this->template());

        $this->actingAs($stranger)
            ->get(route('resumes.pdf', $cv))
            ->assertForbidden();
    }
}
