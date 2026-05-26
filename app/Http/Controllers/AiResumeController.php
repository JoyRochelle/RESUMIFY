<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cv;
use App\Models\ChameleonAdaptation;
use App\Services\AiService;
use Illuminate\Support\Facades\Gate;

class AiResumeController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Refine a single resume bullet point using AI.
     * Premium gating + quota check handled by 'ai.quota' middleware on the route.
     */
    public function refineBullet(Request $request, Cv $cv)
    {
        Gate::authorize('update', $cv);
        
        $request->validate([
            'text' => 'required|string|min:10|max:1000',
            'job_context' => 'nullable|string|max:2000'
        ]);

        $user = auth()->user();

        // Deduct credit BEFORE the AI call
        $user->increment('ai_quota_used', 1);

        try {
            $options = $this->aiService->refineBullet($request->text, $request->job_context);

            // Log usage on success
            $this->aiService->logUsage($user->id, 'bullet_optimize', $cv->id);

            return response()->json(['success' => true, 'options' => $options]);
        } catch (\Exception $e) {
            // Refund the credit on failure
            $user->decrement('ai_quota_used', 1);
            return response()->json(['success' => false, 'message' => 'Failed to refine bullet.'], 500);
        }
    }

    /**
     * Generate 3 CV versions in parallel using different angles.
     * Premium gating + quota check handled by 'ai.quota:3' middleware on the route.
     */
    public function generateVersions(Request $request, Cv $cv)
    {
        Gate::authorize('update', $cv);

        $request->validate([
            'job_description' => 'required|string|min:50|max:10000',
        ]);

        $sections = $cv->sections()->orderBy('order')->get()->map(function($s) {
            return [
                'type' => $s->type,
                'title' => $s->title,
                'content' => $s->content
            ];
        })->toArray();

        $contentLength = 0;
        array_walk_recursive($sections, function($item, $key) use (&$contentLength) {
            if ($key !== 'type' && $key !== 'title' && is_string($item)) {
                $contentLength += strlen(trim($item));
            }
        });

        if ($contentLength < 200) {
            return response()->json(['success' => false, 'message' => 'Your CV does not have enough content to tailor. Please fill in your resume sections with more details first (at least 200 characters).'], 422);
        }

        $user = auth()->user();

        // Deduct 3 credits BEFORE the AI call
        $user->increment('ai_quota_used', 3);

        try {
            $versions = $this->aiService->generateCvVersions($sections, $request->job_description);
            
            $savedVersions = [];
            foreach ($versions as $angle => $adaptedContent) {
                $adaptation = ChameleonAdaptation::create([
                    'cv_id' => $cv->id,
                    'tone_style' => $angle,
                    'adapted_content' => $adaptedContent,
                    'ai_prompt_used' => 'Generated parallel CV version for ' . $angle
                ]);
                
                $savedVersions[] = [
                    'id' => $adaptation->id,
                    'angle' => $angle,
                    'content' => $adaptedContent
                ];
            }

            // Log usage on success
            $this->aiService->logUsage($user->id, 'generate_versions', $cv->id);

            return response()->json(['success' => true, 'versions' => $savedVersions]);
        } catch (\Exception $e) {
            // Refund 3 credits on failure
            $user->decrement('ai_quota_used', 3);
            return response()->json(['success' => false, 'message' => 'Failed to generate CV versions.'], 500);
        }
    }
}
