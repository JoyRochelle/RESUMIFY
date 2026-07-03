<?php

namespace App\Actions\Ai;

use App\Exceptions\AiQuotaExceededException;
use App\Exceptions\InsufficientResumeContentException;
use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\AiService;

class GenerateResumeVersionsAction
{
    public function __construct(
        private AiService $aiService,
        private AiCreditService $aiCreditService,
    ) {}

    public function execute(User $user, Cv $cv, string $jobDescription): array
    {
        $sections = $this->sectionsForGeneration($cv);

        if ($this->contentLength($sections) < 200) {
            throw new InsufficientResumeContentException();
        }

        $reservation = $this->aiCreditService->reserve($user, 3, 'generate_versions');

        if ($reservation->isDenied()) {
            throw new AiQuotaExceededException(
                remainingCredits: $reservation->remainingCredits(),
                quotaLimit: $reservation->quotaLimit,
            );
        }

        try {
            $versions = $this->aiService->generateCvVersions($sections, $jobDescription);
            $savedVersions = [];

            foreach ($versions as $angle => $adaptedContent) {
                $adaptation = ChameleonAdaptation::create([
                    'cv_id' => $cv->id,
                    'tone_style' => $angle,
                    'adapted_content' => $adaptedContent,
                    'ai_prompt_used' => 'Generated parallel CV version for ' . $angle,
                ]);

                $savedVersions[] = [
                    'id' => $adaptation->id,
                    'angle' => $angle,
                    'content' => $adaptedContent,
                ];
            }

            $this->aiService->logUsage($user->id, 'generate_versions', $cv->id);

            return $savedVersions;
        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);

            throw $e;
        }
    }

    private function sectionsForGeneration(Cv $cv): array
    {
        return $cv->sections()
            ->orderBy('order')
            ->get()
            ->map(fn ($section) => [
                'type' => $section->type,
                'title' => $section->title,
                'content' => $section->content,
            ])
            ->toArray();
    }

    private function contentLength(array $sections): int
    {
        $contentLength = 0;

        array_walk_recursive($sections, function ($item, $key) use (&$contentLength): void {
            if (!in_array($key, ['type', 'title'], true) && is_string($item)) {
                $contentLength += strlen(trim($item));
            }
        });

        return $contentLength;
    }
}
