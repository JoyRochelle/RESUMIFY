<?php

namespace Tests\Unit;

use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvSectionSnapshotModelTest extends TestCase
{
    use RefreshDatabase;

    protected function createCv(): Cv
    {
        $user = User::factory()->create();
        $template = CvTemplate::factory()->create();

        return Cv::forceCreate([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'title' => 'Snapshot Test Resume',
        ]);
    }

    public function test_sections_cast_to_array(): void
    {
        $cv = $this->createCv();

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [
                ['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP'], 'order' => 1],
            ],
            'reason' => 'chameleon_apply',
        ]);

        $fresh = $snapshot->fresh();

        $this->assertIsArray($fresh->sections);
        $this->assertSame('PHP', $fresh->sections[0]['content'][0]);
    }

    public function test_belongs_to_cv(): void
    {
        $cv = $this->createCv();

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [],
            'reason' => 'pre_restore',
        ]);

        $this->assertTrue($snapshot->cv->is($cv));
    }

    public function test_belongs_to_source_adaptation_when_present(): void
    {
        $cv = $this->createCv();

        $adaptation = ChameleonAdaptation::create([
            'cv_id' => $cv->id,
            'tone_style' => 'technical',
            'adapted_content' => [],
        ]);

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [],
            'reason' => 'chameleon_apply',
            'source_adaptation_id' => $adaptation->id,
        ]);

        $this->assertTrue($snapshot->sourceAdaptation->is($adaptation));
    }

    public function test_has_no_updated_at_column(): void
    {
        $cv = $this->createCv();

        $snapshot = CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => [],
            'reason' => 'pre_restore',
        ]);

        $this->assertNull(CvSectionSnapshot::UPDATED_AT);
        $this->assertNotNull($snapshot->created_at);
    }
}
