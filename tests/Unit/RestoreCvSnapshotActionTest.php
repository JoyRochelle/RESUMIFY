<?php

namespace Tests\Unit;

use App\Actions\Resumes\RestoreCvSnapshotAction;
use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class RestoreCvSnapshotActionTest extends TestCase
{
    use RefreshDatabase;

    protected function createCvWithSections(): Cv
    {
        $user = User::factory()->create();
        $template = CvTemplate::factory()->create();

        $cv = Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Restore Snapshot Test Resume',
        ]);

        $cv->sections()->create([
            'type' => 'skills',
            'title' => 'Skills',
            'order' => 1,
            'content' => ['PHP', 'Laravel', 'AWS'],
        ]);

        return $cv;
    }

    private function action(): RestoreCvSnapshotAction
    {
        return app(RestoreCvSnapshotAction::class);
    }

    public function test_restores_sections_from_snapshot_content(): void
    {
        $cv = $this->createCvWithSections();

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel'], 'order' => 1],
            ],
            'reason' => 'chameleon_apply',
        ]);

        $this->action()->execute($cv, $snapshot);

        $skills = $cv->sections()->where('type', 'skills')->first();
        $this->assertSame(['PHP', 'Laravel'], $skills->content);
    }

    public function test_creates_a_pre_restore_snapshot_capturing_state_before_restore(): void
    {
        $cv = $this->createCvWithSections();

        $oldSnapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel'], 'order' => 1],
            ],
            'reason' => 'chameleon_apply',
        ]);

        $result = $this->action()->execute($cv, $oldSnapshot);

        $preRestoreSnapshot = CvSectionSnapshot::find($result['snapshot_id']);
        $this->assertNotNull($preRestoreSnapshot);
        $this->assertSame('pre_restore', $preRestoreSnapshot->reason);

        $capturedSkills = collect($preRestoreSnapshot->sections)->firstWhere('type', 'skills');
        $this->assertSame(
            ['PHP', 'Laravel', 'AWS'],
            $capturedSkills['content'],
            'Pre-restore snapshot must capture what the CV looked like BEFORE the restore overwrote it.',
        );
    }

    public function test_recalculates_ats_score_after_restore(): void
    {
        $cv = $this->createCvWithSections();
        $cv->sections()->create([
            'type' => 'target_job',
            'title' => 'Target Job',
            'order' => 2,
            'content' => ['job_title' => 'Laravel Developer'],
        ]);

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['Laravel', 'PHP'], 'order' => 1],
            ],
            'reason' => 'chameleon_apply',
        ]);

        $result = $this->action()->execute($cv, $snapshot);

        $this->assertArrayHasKey('ats_score', $result);
        $this->assertSame($result['ats_score'], $cv->fresh()->ats_score);
    }

    public function test_throws_404_when_snapshot_belongs_to_a_different_cv(): void
    {
        $cv = $this->createCvWithSections();
        $otherCv = $this->createCvWithSections();

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $otherCv->id,
            'sections' => [],
            'reason' => 'chameleon_apply',
        ]);

        $this->expectException(NotFoundHttpException::class);

        $this->action()->execute($cv, $snapshot);
    }
}
