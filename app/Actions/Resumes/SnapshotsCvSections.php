<?php

namespace App\Actions\Resumes;

use App\Models\Cv;
use App\Models\CvSectionSnapshot;
use Illuminate\Support\Facades\Log;

trait SnapshotsCvSections
{
    private function snapshotCurrentSections(Cv $cv, string $reason, ?string $sourceAdaptationId = null): CvSectionSnapshot
    {
        $sections = $cv->sections->map(fn ($section) => [
            'type' => $section->type,
            'title' => $section->title,
            'content' => $section->content,
            'order' => $section->order,
        ])->all();

        return CvSectionSnapshot::create([
            'cv_id' => $cv->id,
            'sections' => $sections,
            'reason' => $reason,
            'source_adaptation_id' => $sourceAdaptationId,
        ]);
    }

    private function applySectionsFromPayload(Cv $cv, array $sections): void
    {
        foreach ($sections as $item) {
            $section = $cv->sections->firstWhere('type', $item['type'] ?? null);

            if (!$section) {
                Log::warning('Skipped applying section with no matching type on CV', [
                    'cv_id' => $cv->id,
                    'type' => $item['type'] ?? null,
                ]);

                continue;
            }

            $section->fill([
                'title' => $item['title'] ?? $section->title,
                'content' => $item['content'] ?? $section->content,
            ]);
            $section->forceFill(['last_saved_at' => now()]);
            $section->save();

            if ($section->type === 'target_job' && isset($item['content']['job_title'])) {
                $cv->update(['job_target' => $item['content']['job_title']]);
            }
        }
    }
}
