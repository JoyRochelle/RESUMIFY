<?php

namespace App\Http\Controllers\User\Resume;

use App\Actions\Resumes\RestoreCvSnapshotAction;
use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ResumeSnapshotController extends Controller
{
    /**
     * List this CV's section snapshots, newest first.
     */
    public function index(Cv $cv): JsonResponse
    {
        Gate::authorize('view', $cv);

        $snapshots = $cv->snapshots()
            ->latest()
            ->get(['id', 'reason', 'source_adaptation_id', 'created_at']);

        return response()->json(['snapshots' => $snapshots]);
    }

    /**
     * Restore the CV's sections to a prior snapshot.
     * The current state is snapshotted first (reason: pre_restore).
     */
    public function restore(
        Cv $cv,
        CvSectionSnapshot $snapshot,
        RestoreCvSnapshotAction $restoreCvSnapshot,
    ): JsonResponse {
        Gate::authorize('update', $cv);

        $result = $restoreCvSnapshot->execute($cv, $snapshot);

        return response()->json([
            'success' => true,
            'saved_at' => now()->format('H:i:s'),
            'ats_score' => $result['ats_score'],
            'ats_matched' => $result['ats_matched'],
            'snapshot_id' => $result['snapshot_id'],
        ]);
    }
}
