<?php

namespace Tests\Feature;

use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplyChameleonAdaptationFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        CvTemplate::create([
            'id' => \Illuminate\Support\Str::ulid(),
            'name' => 'Default Template',
            'blade_path' => 'templates.default',
            'is_active' => true,
            'is_premium' => false,
        ]);
    }

    protected function createCvForUser(User $user): Cv
    {
        $cv = Cv::forceCreate([
            'id' => \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'template_id' => CvTemplate::first()->id,
            'title' => 'My Test Resume',
            'status' => 'draft',
        ]);

        $cv->sections()->create([
            'type' => 'skills',
            'title' => 'Skills',
            'order' => 1,
            'content' => ['PHP', 'Laravel'],
        ]);

        return $cv;
    }

    public function test_owner_can_apply_a_generated_version(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);

        $chosen = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'technical',
            'adapted_content' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel', 'AWS']],
            ],
        ]);
        $sibling = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'leadership',
            'adapted_content' => [],
        ]);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/versions/{$chosen->id}/apply");

        $response->assertStatus(200)->assertJson(['success' => true]);

        $skills = $cv->sections()->where('type', 'skills')->first();
        $this->assertSame(['PHP', 'Laravel', 'AWS'], $skills->content);

        $this->assertNotNull(ChameleonAdaptation::find($chosen->id));
        $this->assertNull(ChameleonAdaptation::find($sibling->id));

        $this->assertDatabaseCount('cv_section_snapshots', 1);
    }

    public function test_non_owner_cannot_apply_a_version(): void
    {
        $owner = User::factory()->create(['role' => 'basic']);
        $attacker = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($owner);

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'tone_style' => 'technical',
            'adapted_content' => [],
        ]);

        $response = $this->actingAs($attacker)->postJson("/resumes/{$cv->id}/ai/versions/{$adaptation->id}/apply");

        $response->assertStatus(403);
    }

    public function test_applying_adaptation_belonging_to_a_different_cv_returns_404(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);
        $otherCv = $this->createCvForUser($user);

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $otherCv->id,
            'tone_style' => 'technical',
            'adapted_content' => [],
        ]);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/versions/{$adaptation->id}/apply");

        $response->assertStatus(404);
    }

    public function test_applying_a_version_with_fabrication_warning_still_succeeds(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'tone_style' => 'technical',
            'adapted_content' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel', 'Google Cloud Certified']],
            ],
        ]);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/ai/versions/{$adaptation->id}/apply");

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}
