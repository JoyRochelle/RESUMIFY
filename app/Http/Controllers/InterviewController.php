<?php

namespace App\Http\Controllers;

use App\Models\AiUsageLog;
use App\Models\Cv;
use App\Models\InterviewSession;
use App\Services\InterviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class InterviewController extends Controller
{
    public function __construct(protected InterviewService $interviewService) {}

    /**
     * Show the resume-picker / job-target form to start a new interview.
     */
    public function index(Request $request): View
    {
        $cvs = auth()->user()->cvs()->with('sections')->latest()->get();

        return view('user.interview.index', compact('cvs'));
    }

    /**
     * Show the live chat page for an active (or completed) session.
     */
    public function show(InterviewSession $session): View
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $session->load('messages');

        return view('user.interview.session', compact('session'));
    }

    /**
     * Mark the session as completed and redirect back to the start page.
     * I6-03 will update this redirect to go to the feedback report.
     */
    public function endSession(Request $request, InterviewSession $session): RedirectResponse
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        if ($session->status !== 'active') {
            return redirect()->back()->with('error', 'Sesi ini sudah selesai.');
        }

        $session->update([
            'status'   => 'completed',
            'ended_at' => now(),
        ]);

        return redirect()->route('interview.index')
            ->with('success', 'Sesi wawancara selesai. Terima kasih!');
    }

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
