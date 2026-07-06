<?php

namespace Tests\Unit;

use App\Actions\Resumes\ApplyChameleonAdaptationAction;
use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ApplyChameleonAdaptationActionTest extends TestCase
{
    use RefreshDatabase;

    protected function createCvWithSections(): Cv
    {
        $user = User::factory()->create();
        $template = CvTemplate::factory()->create();

        $cv = Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Apply Version Test Resume',
        ]);

        $cv->sections()->create([
            'type' => 'skills',
            'title' => 'Skills',
            'order' => 1,
            'content' => ['PHP', 'Laravel'],
        ]);

        $cv->sections()->create([
            'type' => 'target_job',
            'title' => 'Target Job',
            'order' => 2,
            'content' => ['job_title' => 'Old Title', 'job_company' => 'Old Co'],
        ]);

        return $cv;
    }

    private function action(): ApplyChameleonAdaptationAction
    {
        return app(ApplyChameleonAdaptationAction::class);
    }

    public function test_overwrites_matching_sections_with_adapted_content(): void
    {
        $cv = $this->createCvWithSections();

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'technical',
            'adapted_content' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel', 'AWS']],
            ],
        ]);

        $this->action()->execute($cv, $adaptation);

        $skills = $cv->sections()->where('type', 'skills')->first();
        $this->assertSame(['PHP', 'Laravel', 'AWS'], $skills->content);
    }

    public function test_creates_snapshot_of_prior_content_before_overwriting(): void
    {
        $cv = $this->createCvWithSections();

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'technical',
            'adapted_content' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP', 'Laravel', 'AWS']],
            ],
        ]);

        $this->action()->execute($cv, $adaptation);

        $snapshot = CvSectionSnapshot::where('cv_id', $cv->id)->first();
        $this->assertNotNull($snapshot);
        $this->assertSame('chameleon_apply', $snapshot->reason);
        $this->assertSame($adaptation->id, $snapshot->source_adaptation_id);

        $snapshottedSkills = collect($snapshot->sections)->firstWhere('type', 'skills');
        $this->assertSame(['PHP', 'Laravel'], $snapshottedSkills['content'], 'Snapshot must capture the OLD content, not the new content.');
    }

    public function test_syncs_job_target_when_target_job_section_present_in_adapted_content(): void
    {
        $cv = $this->createCvWithSections();

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'technical',
            'adapted_content' => [
                ['type' => 'target_job', 'title' => 'Target Job', 'content' => ['job_title' => 'New Title', 'job_company' => 'New Co']],
            ],
        ]);

        $this->action()->execute($cv, $adaptation);

        $this->assertSame('New Title', $cv->fresh()->job_target);
    }

    public function test_recalculates_ats_score_after_overwrite(): void
    {
        $cv = $this->createCvWithSections();
        $cv->sections()->create([
            'type' => 'personal_info',
            'title' => 'Personal Info',
            'order' => 3,
            'content' => ['summary' => 'Just a summary.'],
        ]);

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'technical',
            'adapted_content' => [
                ['type' => 'target_job', 'title' => 'Target Job', 'content' => ['job_title' => 'Laravel Developer', 'job_company' => 'Acme']],
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['Laravel', 'PHP']],
            ],
        ]);

        $result = $this->action()->execute($cv, $adaptation);

        $this->assertArrayHasKey('ats_score', $result);
        $this->assertSame($result['ats_score'], $cv->fresh()->ats_score);
    }

    public function test_deletes_batch_siblings_but_keeps_chosen_adaptation(): void
    {
        $cv = $this->createCvWithSections();

        $chosen = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'technical',
            'adapted_content' => [['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP']]],
        ]);
        $sibling1 = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'leadership',
            'adapted_content' => [],
        ]);
        $sibling2 = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'ownership',
            'adapted_content' => [],
        ]);

        $this->action()->execute($cv, $chosen);

        $this->assertNotNull(ChameleonAdaptation::find($chosen->id), 'Chosen adaptation must survive for provenance.');
        $this->assertNull(ChameleonAdaptation::find($sibling1->id));
        $this->assertNull(ChameleonAdaptation::find($sibling2->id));
    }

    public function test_does_not_delete_adaptations_with_different_or_null_batch_id(): void
    {
        $cv = $this->createCvWithSections();

        $chosen = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch01',
            'tone_style' => 'technical',
            'adapted_content' => [['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP']]],
        ]);
        $unrelatedBatch = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => 'batch02',
            'tone_style' => 'leadership',
            'adapted_content' => [],
        ]);
        $legacyNoBatch = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'batch_id' => null,
            'tone_style' => 'ownership',
            'adapted_content' => [],
        ]);

        $this->action()->execute($cv, $chosen);

        $this->assertNotNull(ChameleonAdaptation::find($unrelatedBatch->id));
        $this->assertNotNull(ChameleonAdaptation::find($legacyNoBatch->id));
    }

    public function test_throws_404_when_adaptation_belongs_to_a_different_cv(): void
    {
        $cv = $this->createCvWithSections();
        $otherCv = $this->createCvWithSections();

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $otherCv->id,
            'tone_style' => 'technical',
            'adapted_content' => [],
        ]);

        $this->expectException(NotFoundHttpException::class);

        $this->action()->execute($cv, $adaptation);
    }
}
