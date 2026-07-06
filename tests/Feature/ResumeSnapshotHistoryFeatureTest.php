<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeSnapshotHistoryFeatureTest extends TestCase
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

    public function test_owner_sees_only_their_own_cv_snapshots_newest_first(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);
        $otherCv = $this->createCvForUser(User::factory()->create(['role' => 'basic']));

        $older = CvSectionSnapshot::create(['cv_id' => $cv->id, 'sections' => [], 'reason' => 'chameleon_apply']);
        sleep(1);
        $newer = CvSectionSnapshot::create(['cv_id' => $cv->id, 'sections' => [], 'reason' => 'pre_restore']);
        CvSectionSnapshot::create(['cv_id' => $otherCv->id, 'sections' => [], 'reason' => 'chameleon_apply']);

        $response = $this->actingAs($user)->getJson("/resumes/{$cv->id}/history");

        $response->assertStatus(200);
        $ids = collect($response->json('snapshots'))->pluck('id');

        $this->assertCount(2, $ids);
        $this->assertSame($newer->id, $ids->first());
        $this->assertSame($older->id, $ids->last());
    }

    public function test_non_owner_cannot_list_snapshots(): void
    {
        $owner = User::factory()->create(['role' => 'basic']);
        $attacker = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($owner);

        $response = $this->actingAs($attacker)->getJson("/resumes/{$cv->id}/history");

        $response->assertStatus(403);
    }

    public function test_owner_can_restore_a_snapshot_and_history_grows_by_one(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel', 'Old'], 'order' => 1],
            ],
            'reason' => 'chameleon_apply',
        ]);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/history/{$snapshot->id}/restore");

        $response->assertStatus(200)->assertJson(['success' => true]);

        $skills = $cv->sections()->where('type', 'skills')->first();
        $this->assertSame(['PHP', 'Laravel', 'Old'], $skills->content);

        $this->assertDatabaseCount('cv_section_snapshots', 2);
        $this->assertDatabaseHas('cv_section_snapshots', ['cv_id' => $cv->id, 'reason' => 'pre_restore']);
    }

    public function test_non_owner_cannot_restore_a_snapshot(): void
    {
        $owner = User::factory()->create(['role' => 'basic']);
        $attacker = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($owner);

        $snapshot = CvSectionSnapshot::create(['cv_id' => $cv->id, 'sections' => [], 'reason' => 'chameleon_apply']);

        $response = $this->actingAs($attacker)->postJson("/resumes/{$cv->id}/history/{$snapshot->id}/restore");

        $response->assertStatus(403);
    }

    public function test_restoring_a_snapshot_belonging_to_a_different_cv_returns_404(): void
    {
        $user = User::factory()->create(['role' => 'basic']);
        $cv = $this->createCvForUser($user);
        $otherCv = $this->createCvForUser($user);

        $snapshot = CvSectionSnapshot::create(['cv_id' => $otherCv->id, 'sections' => [], 'reason' => 'chameleon_apply']);

        $response = $this->actingAs($user)->postJson("/resumes/{$cv->id}/history/{$snapshot->id}/restore");

        $response->assertStatus(404);
    }
}
