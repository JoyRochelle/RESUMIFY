<?php

namespace App\Actions\Ai;

use App\Exceptions\AiQuotaExceededException;
use App\Exceptions\InsufficientResumeContentException;
use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\AiService;
use App\Services\CvFactualityValidator;
use Illuminate\Support\Str;

class GenerateResumeVersionsAction
{
    public function __construct(
        private AiService $aiService,
        private AiCreditService $aiCreditService,
        private CvFactualityValidator $factualityValidator,
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
            $batchId = (string) Str::ulid();

            foreach ($versions as $angle => $adaptedContent) {
                $adaptation = ChameleonAdaptation::create([
                    'cv_id' => $cv->id,
                    'batch_id' => $batchId,
                    'tone_style' => $angle,
                    'adapted_content' => $adaptedContent,
                    'ai_prompt_used' => 'Generated parallel CV version for ' . $angle,
                ]);

                $factuality = $this->factualityValidator->validate($sections, $adaptedContent);

                $savedVersions[] = [
                    'id' => $adaptation->id,
                    'angle' => $angle,
                    'content' => $adaptedContent,
                    'warning' => $factuality['flagged'] ? $factuality : null,
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
