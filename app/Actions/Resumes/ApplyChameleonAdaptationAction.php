<?php

namespace App\Actions\Resumes;

use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Services\AtsScoreService;
use Illuminate\Support\Facades\DB;

class ApplyChameleonAdaptationAction
{
    use SnapshotsCvSections;

    public function execute(Cv $cv, ChameleonAdaptation $adaptation): array
    {
        abort_unless($adaptation->cv_id === $cv->id, 404);

        return DB::transaction(function () use ($cv, $adaptation) {
            $cv->loadMissing('sections');

            $snapshot = $this->snapshotCurrentSections($cv, 'chameleon_apply', $adaptation->id);

            $this->applySectionsFromPayload($cv, $adaptation->adapted_content ?? []);

            $atsResult = AtsScoreService::calculate($cv);
            $cv->forceFill(['ats_score' => $atsResult['score']])->save();

            if ($adaptation->batch_id !== null) {
                ChameleonAdaptation::where('cv_id', $cv->id)
                    ->where('batch_id', $adaptation->batch_id)
                    ->where('id', '!=', $adaptation->id)
                    ->delete();
            }

            return [
                'ats_score' => $atsResult['score'],
                'ats_matched' => $atsResult['matched'],
                'snapshot_id' => $snapshot->id,
            ];
        });
    }
}
