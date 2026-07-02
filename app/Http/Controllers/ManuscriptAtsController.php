<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Services\AiService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ManuscriptAtsController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Score a CV's content using Gemini and return an ATS score (0-100).
     * This is called automatically after each section save in the manuscript editor.
     * Free feature — no AI credit deducted.
     */
    public function score(Request $request, Cv $cv): JsonResponse
    {
        Gate::authorize('view', $cv);

        // Flatten the CV content into readable text for analysis
        $cv->load('sections');
        $resumeText = $this->flattenCv($cv);
        
        $targetJob = $cv->sections->where('type', 'target_job')->first();
        $jobTitle = $targetJob->content['job_title'] ?? null;
        $jobCompany = $targetJob->content['job_company'] ?? null;
        $jobDescription = $targetJob->content['job_description'] ?? null;

        if (strlen(trim($resumeText)) < 30) {
            return response()->json(['score' => 0, 'tip' => 'Add more content to get your ATS score.']);
        }

        try {
            $data = $this->aiService->scoreResume($resumeText, $jobTitle, $jobCompany, $jobDescription);

            // Clamp score to 0-100
            $data['score'] = max(0, min(100, (int) ($data['score'] ?? 0)));

            return response()->json($data);

        } catch (ConnectionException $e) {
            Log::error('ManuscriptAts Timeout', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'AI service timed out.'], 504);
        } catch (\Exception $e) {
            Log::error('ManuscriptAts Exception', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Convert CV sections to plain text for the AI prompt.
     */
    private function flattenCv(Cv $cv): string
    {
        $lines = [];

        foreach ($cv->sections->sortBy('order') as $section) {
            // Exclude target_job from the resume text itself (it's the context, not the content)
            if ($section->type === 'target_job') continue;
            
            $content = $section->content;
            if (empty($content)) continue;

            $lines[] = strtoupper($section->title) . ':';

            // Check if it's a list (array of objects) or a flat object
            if (isset($content[0]) && is_array($content[0])) {
                // List-based (experience, education, skills)
                foreach ($content as $item) {
                    if (is_array($item)) {
                        $lines[] = '• ' . implode(' | ', array_filter(array_values($item)));
                    }
                }
            } else {
                // Flat object (personal_info, etc.)
                foreach ($content as $key => $value) {
                    if (!empty($value) && is_string($value)) {
                        $lines[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
                    }
                }
            }

            $lines[] = '';
        }

        return implode("\n", $lines);
    }
}
