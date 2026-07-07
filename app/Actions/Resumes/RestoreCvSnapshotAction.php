<?php

namespace App\Actions\Resumes;

use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use App\Services\AtsScoreService;
use Illuminate\Support\Facades\DB;

class RestoreCvSnapshotAction
{
    use SnapshotsCvSections;

    public function execute(Cv $cv, CvSectionSnapshot $snapshot): array
    {
        abort_unless($snapshot->cv_id === $cv->id, 404);

        return DB::transaction(function () use ($cv, $snapshot) {
            $cv->loadMissing('sections');

            $preRestoreSnapshot = $this->snapshotCurrentSections($cv, 'pre_restore');

            $this->applySectionsFromPayload($cv, $snapshot->sections ?? []);

            $atsResult = AtsScoreService::calculate($cv);
            $cv->forceFill(['ats_score' => $atsResult['score']])->save();

            return [
                'ats_score' => $atsResult['score'],
                'ats_matched' => $atsResult['matched'],
                'snapshot_id' => $preRestoreSnapshot->id,
            ];
        });
    }
}
