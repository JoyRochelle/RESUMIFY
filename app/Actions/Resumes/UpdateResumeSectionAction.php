<?php

namespace App\Actions\Resumes;

use App\Models\Cv;
use App\Models\CvSection;
use App\Services\AtsScoreService;
use InvalidArgumentException;

class UpdateResumeSectionAction
{
    /**
     * @return array{section: CvSection, ats_score: int, ats_matched: array}
     */
    public function execute(
        Cv $resume,
        CvSection $section,
        ?string $title = null,
        mixed $content = null,
        bool $replaceContent = false
    ): array {
        $data = [
            'title' => $title ?? $section->title,
            'last_saved_at' => now(),
        ];

        if ($replaceContent) {
            $data['content'] = $this->normalizeContent($content);
        }

        $section->update($data);

        if ($section->type === 'target_job' && isset($data['content']['job_title'])) {
            $resume->update(['job_target' => $data['content']['job_title']]);
        }

        $atsResult = AtsScoreService::calculate($resume);
        $resume->update(['ats_score' => $atsResult['score']]);

        return [
            'section' => $section,
            'ats_score' => $atsResult['score'],
            'ats_matched' => $atsResult['matched'],
        ];
    }

    private function normalizeContent(mixed $content): mixed
    {
        if (!is_string($content) || $content === '') {
            return $content;
        }

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('Invalid JSON format.');
        }

        return $decoded;
    }
}
