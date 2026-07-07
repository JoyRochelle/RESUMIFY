<?php

namespace App\Http\Controllers\User\Interview;

use App\Actions\Interviews\EndInterviewSessionAction;
use App\Actions\Interviews\GenerateInterviewFeedbackAction;
use App\Actions\Interviews\SendInterviewMessageAction;
use App\Actions\Interviews\StartInterviewAction;
use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\Cv;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Queries\InterviewTrendQuery;
use App\Services\AiCreditService;
use App\Services\InterviewService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InterviewController extends Controller
{
    public function __construct(
        protected InterviewService $interviewService,
        protected AiCreditService $aiCreditService,
        protected InterviewTrendQuery $trendQuery,
    ) {}

    /**
     * Show the resume-picker / job-target form to start a new interview.
     */
    public function index(Request $request): View
    {
        $user      = auth()->user();
        $cvs       = $user->cvs()->with('sections')->latest()->get();
        $trialUsed = !$user->isPremium() && !$user->isAdmin()
                     && $user->interviewSessions()->exists();

        $recentSessions = $user->interviewSessions()
            ->with(['cv', 'feedback'])
            ->orderByDesc('started_at')
            ->take(3)
            ->get();

        return view('user.interview.index', compact('cvs', 'trialUsed', 'recentSessions'));
    }

    /**
     * Show the live chat page for an active (or completed) session.
     */
    public function show(InterviewSession $session): View
    {
        Gate::authorize('view', $session);

        $session->load('messages', 'feedback');

        return view('user.interview.session', compact('session'));
    }

    /**
     * Mark the session as completed, generate AI feedback, and redirect to the report page.
     */
    public function endSession(
        Request $request,
        InterviewSession $session,
        EndInterviewSessionAction $endInterviewSession
    ): RedirectResponse
    {
        Gate::authorize('endSession', $session);

        if ($session->status !== 'active') {
            return redirect()->back()->with('error', 'This session has already ended.');
        }

        $result = $endInterviewSession->execute(auth()->user(), $session);

        if ($result->feedbackUnavailableDueToQuota()) {
            return redirect()->route('interview.index')
                ->with('success', 'Session ended! Feedback report is unavailable — your AI credits are exhausted.');
        }

        if ($result->feedbackGeneratedSuccessfully()) {
            return redirect()->route('interview.feedback', $session)
                ->with('success', 'Session ended! Here is your interview report.');
        }

        if ($result->feedbackInvalidProviderResponse()) {
            return redirect()->route('interview.index')
                ->with('error', ApiResponse::AI_PROVIDER_INVALID_RESPONSE);
        }

        return redirect()->route('interview.index')
            ->with('success', 'Session ended. Feedback report could not be generated — please try again later.');
    }

    /**
     * Show the AI-generated feedback report for a completed session.
     */
    public function feedback(InterviewSession $session): View|RedirectResponse
    {
        Gate::authorize('feedback', $session);

        $session->load('feedback');

        if (!$session->feedback) {
            return redirect()->route('interview.show', $session);
        }

        return view('user.interview.feedback', compact('session'));
    }

    /**
     * Regenerate the feedback report for a completed session that has none —
     * recovery path for sessions that ended while feedback generation failed.
     * Quota: 1 credit, reserved and refunded inside the action.
     */
    public function generateFeedback(
        Request $request,
        InterviewSession $session,
        GenerateInterviewFeedbackAction $generateInterviewFeedback
    ): RedirectResponse {
        Gate::authorize('feedback', $session);

        if ($session->status === 'active') {
            return redirect()->route('interview.show', $session)
                ->with('error', 'End the session first to generate its report.');
        }

        $session->load('feedback');

        if ($session->feedback) {
            return redirect()->route('interview.feedback', $session);
        }

        $result = $generateInterviewFeedback->execute(auth()->user(), $session);

        if ($result->feedbackGeneratedSuccessfully()) {
            return redirect()->route('interview.feedback', $session)
                ->with('success', 'Here is your interview report.');
        }

        if ($result->feedbackUnavailableDueToQuota()) {
            return redirect()->route('interview.show', $session)
                ->with('error', 'Report is unavailable — your AI credits are exhausted.');
        }

        return redirect()->route('interview.show', $session)
            ->with('error', 'Failed to generate the report. Please try again.');
    }

    /**
     * List all interview sessions for the authenticated user.
     */
    public function history(Request $request): View
    {
        $user = auth()->user();
        $cvs  = $user->cvs()->latest()->get();

        $query = $user->interviewSessions()
            ->with(['cv', 'feedback'])
            ->leftJoin('interview_feedback as f', 'interview_sessions.id', '=', 'f.session_id')
            ->select('interview_sessions.*')
            ->when($request->filled('cv_id'), fn($q) => $q->where('resume_id', $request->cv_id));

        $sort  = $request->get('sort', 'date');
        $order = $request->get('order', 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'score') {
            $query->orderByRaw('CASE WHEN f.overall_score IS NULL THEN 1 ELSE 0 END')
                  ->orderBy('f.overall_score', $order);
        } else {
            $query->orderBy('interview_sessions.started_at', $order);
        }

        $sessions = $query->paginate(10)->withQueryString();

        $trends = $this->trendQuery->getTrendsForSessions($sessions->getCollection(), $user->id);

        return view('user.interview.history', compact('sessions', 'cvs', 'trends', 'sort', 'order'));
    }



    /**
     * Stream Ms. Sarah's reply token-by-token via SSE.
     * Quota: 1 credit (handled by ai.quota middleware on the route).
     */
    public function stream(Request $request, InterviewSession $session): StreamedResponse
    {
        Gate::authorize('stream', $session);

        $request->validate(['content' => 'required|string|max:2000']);

        if ($session->status !== 'active') {
            return response()->stream(function () {
                echo 'data: ' . json_encode(['error' => 'Session is no longer active.']) . "\n\n";
            }, 422, ['Content-Type' => 'text/event-stream', 'Cache-Control' => 'no-cache']);
        }

        $user    = auth()->user();
        $content = $request->content;

        // Save user message before stream so tests can assert on it without triggering the closure
        InterviewMessage::create([
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => $content,
        ]);

        $session->load('cv.sections');
        $systemPrompt = $this->interviewService->buildSystemPrompt($session->cv, $session->job_target);
        $contents = $this->interviewService->buildRecentConversationContents($session);

        return response()->stream(function () use ($user, $session, $systemPrompt, $contents) {
            $reservation = $this->aiCreditService->reserve($user, 1, 'interview_stream');

            if ($reservation->isDenied()) {
                echo 'data: ' . json_encode(['error' => 'You have used all your AI credits. Upgrade to Premium for 50 credits/month.']) . "\n\n";
                $this->flushSse();

                return;
            }

            try {
                $fullText = $this->interviewService->callGeminiStreaming(
                    $systemPrompt,
                    $contents,
                    function (string $token) {
                        echo 'data: ' . json_encode(['token' => $token]) . "\n\n";
                        $this->flushSse();
                    }
                );

                if (trim($fullText) === '') {
                    throw new \Exception('Empty response from AI service');
                }

                InterviewMessage::create([
                    'session_id' => $session->id,
                    'role'       => 'assistant',
                    'content'    => $fullText,
                ]);

                AiUsageLog::create([
                    'user_id'     => $user->id,
                    'action_type' => 'interview_question',
                    'resume_id'   => $session->resume_id,
                    'tokens_used' => 0,
                    'cost_usd'    => 0,
                ]);

                echo 'data: ' . json_encode(['done' => true]) . "\n\n";
                $this->flushSse();

            } catch (\Exception $e) {
                $this->aiCreditService->refund($reservation);
                Log::error('InterviewController@stream failed', ['error' => $e->getMessage()]);
                echo 'data: ' . json_encode(['error' => 'Failed to get AI response.']) . "\n\n";
                $this->flushSse();
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    /**
     * Flush the SSE output buffer if one is active. `php artisan serve` (and
     * some other SAPIs) run requests with zero output buffering, so an
     * unguarded ob_flush() raises an ErrorException that would otherwise
     * kill the stream from inside its own catch block.
     */
    private function flushSse(): void
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }

    /**
     * Start a new interview session and return Bu Sari's opening question.
     * Quota: 1 credit (handled by ai.quota middleware on the route).
     */
    public function start(Request $request, StartInterviewAction $startInterview): JsonResponse
    {
        $request->validate([
            'cv_id'      => 'required|string|exists:cvs,id',
            'job_target' => 'required|string|max:200',
        ]);

        $cv = Cv::findOrFail($request->cv_id);
        Gate::authorize('view', $cv);

        $user = auth()->user();

        try {
            $result = $startInterview->execute($user, $cv, $request->job_target);

            return response()->json([
                'success'    => true,
                'session_id' => $result['session']->id,
                'message'    => $result['message'],
                'quota'      => $user->aiQuotaSnapshot(),
            ]);
        } catch (HttpExceptionInterface $e) {
            if ($e->getStatusCode() === 402) {
                return response()->json([
                    'error'     => 'quota_exceeded',
                    'message'   => $user->isPremium()
                        ? 'You have used all your AI credits for this month.'
                        : sprintf('You have used all your AI credits. Upgrade to Premium for %d credits/month.', config('quota.premium')),
                    'remaining' => $user->getQuotaRemaining(),
                    'limit'     => $user->getQuotaLimit(),
                ], 402);
            }

            throw $e;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to start interview session.'], 500);
        }
    }

    /**
     * Send a user message and return Bu Sari's next question.
     * Quota: 1 credit per exchange (handled by ai.quota middleware on the route).
     */
    public function message(
        Request $request,
        InterviewSession $session,
        SendInterviewMessageAction $sendInterviewMessage
    ): JsonResponse
    {
        Gate::authorize('message', $session);

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        if ($session->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'This interview session is no longer active.'], 422);
        }

        $user = auth()->user();

        try {
            $reply = $sendInterviewMessage->execute($user, $session, $request->content);

            return response()->json([
                'success' => true,
                'message' => $reply,
                'quota'   => $user->aiQuotaSnapshot(),
            ]);
        } catch (HttpExceptionInterface $e) {
            if ($e->getStatusCode() === 402) {
                return response()->json([
                    'error'     => 'quota_exceeded',
                    'message'   => $user->isPremium()
                        ? 'You have used all your AI credits for this month.'
                        : sprintf('You have used all your AI credits. Upgrade to Premium for %d credits/month.', config('quota.premium')),
                    'remaining' => $user->getQuotaRemaining(),
                    'limit'     => $user->getQuotaLimit(),
                ], 402);
            }

            throw $e;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to get AI response.'], 500);
        }
    }
}
