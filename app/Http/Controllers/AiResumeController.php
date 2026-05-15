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

    public function refineBullet(Request $request, Cv $cv)
    {
        // For Premium only
        if (!auth()->user()->isPremium() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Premium feature only.'], 403);
        }

        Gate::authorize('update', $cv);
        
        $request->validate([
            'text' => 'required|string|max:1000',
            'job_context' => 'nullable|string|max:2000'
        ]);

        try {
            $options = $this->aiService->refineBullet($request->text, $request->job_context);
            if (isset(auth()->user()->ai_quota_used)) {
                auth()->user()->increment('ai_quota_used', 1);
            }

            return response()->json(['success' => true, 'options' => $options]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to refine bullet.'], 500);
        }
    }

    public function generateVersions(Request $request, Cv $cv)
    {
        if (!auth()->user()->isPremium() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Premium feature only.'], 403);
        }

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

            if (isset(auth()->user()->ai_quota_used)) {
                auth()->user()->increment('ai_quota_used', 3);
            }

            return response()->json(['success' => true, 'versions' => $savedVersions]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate CV versions.'], 500);
        }
    }
}
