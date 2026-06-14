<?php

namespace App\Http\Controllers;

use App\Models\AiUsageLog;
use App\Models\Cv;
use App\Models\InterviewSession;
use App\Services\InterviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    public function __construct(protected InterviewService $interviewService) {}

    /**
     * Start a new interview session and return Bu Sari's opening question.
     * Quota: 1 credit (handled by ai.quota middleware on the route).
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'cv_id'      => 'required|string|exists:cvs,id',
            'job_target' => 'required|string|max:200',
        ]);

        $cv = Cv::findOrFail($request->cv_id);
        Gate::authorize('view', $cv);

        $user = auth()->user();

        $user->increment('ai_quota_used', 1);

        try {
            $result = $this->interviewService->startSession($user, $cv, $request->job_target);

            AiUsageLog::create([
                'user_id'     => $user->id,
                'action_type' => 'interview_question',
                'resume_id'   => $cv->id,
                'tokens_used' => 0,
                'cost_usd'    => 0,
            ]);

            return response()->json([
                'success'    => true,
                'session_id' => $result['session']->id,
                'message'    => $result['message'],
            ]);
        } catch (\Exception $e) {
            $user->decrement('ai_quota_used', 1);
            Log::error('InterviewController@start failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to start interview session.'], 500);
        }
    }

    /**
     * Send a user message and return Bu Sari's next question.
     * Quota: 1 credit per exchange (handled by ai.quota middleware on the route).
     */
    public function message(Request $request, InterviewSession $session): JsonResponse
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        if ($session->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'This interview session is no longer active.'], 422);
        }

        $user = auth()->user();

        $user->increment('ai_quota_used', 1);

        try {
            $reply = $this->interviewService->sendMessage($session, $request->content);

            AiUsageLog::create([
                'user_id'     => $user->id,
                'action_type' => 'interview_question',
                'resume_id'   => $session->resume_id,
                'tokens_used' => 0,
                'cost_usd'    => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => $reply,
            ]);
        } catch (\Exception $e) {
            $user->decrement('ai_quota_used', 1);
            Log::error('InterviewController@message failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to get AI response.'], 500);
        }
    }
}
