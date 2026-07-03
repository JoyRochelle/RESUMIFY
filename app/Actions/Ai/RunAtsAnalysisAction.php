<?php

namespace App\Actions\Ai;

use App\Exceptions\AiQuotaExceededException;
use App\Models\AtsScan;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\AiService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class RunAtsAnalysisAction
{
    public function __construct(
        private AiService $aiService,
        private AiCreditService $aiCreditService,
    ) {}

    public function execute(User $user, array $data): array
    {
        $reservation = $this->aiCreditService->reserve($user, 1, 'ats_analyze');

        if ($reservation->isDenied()) {
            throw new AiQuotaExceededException(
                remainingCredits: $reservation->remainingCredits(),
                quotaLimit: $reservation->quotaLimit,
            );
        }

        try {
            $analysis = $this->aiService->analyzeAts($data['resume'], $data['job_description']);
            $analysis['word_count'] = str_word_count($data['resume']);

            $scan = AtsScan::create([
                'user_id' => $user->id,
                'cv_id' => $data['cv_id'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'job_company' => $data['job_company'] ?? null,
                'job_description' => $data['job_description'],
                'score' => $analysis['score'] ?? null,
                'matched_keywords' => $analysis['matched'] ?? null,
                'result_json' => $analysis,
            ]);

            $this->aiService->logUsage($user->id, 'ats_analyze');

            $analysis['_scan_id'] = $scan->id;
            $analysis['_scan_created'] = $scan->created_at->toISOString();

            return $analysis;
        } catch (ConnectionException $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('ATS Connection Timeout', ['message' => $e->getMessage()]);

            throw $e;
        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('ATS Analysis Exception', ['message' => $e->getMessage()]);

            throw $e;
        }
    }
}
