<?php

namespace App\Services;

use App\Models\Cv;
use App\Models\InterviewFeedback;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InterviewService
{
    private const GEMINI_URL        = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    private const GEMINI_STREAM_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:streamGenerateContent';
    public const RECENT_MESSAGE_LIMIT = 20;

    /**
     * Create an interview session and ask Bu Sari's opening question.
     *
     * @return array{session: InterviewSession, message: string}
     */
    public function startSession(User $user, Cv $cv, string $jobTarget): array
    {
        $cv->load('sections');

        $session = InterviewSession::create([
            'user_id'    => $user->id,
            'resume_id'  => $cv->id,
            'job_target' => $jobTarget,
            'status'     => 'active',
            'started_at' => now(),
        ]);

        $systemPrompt = $this->buildSystemPrompt($cv, $jobTarget);

        // Trigger Ms. Sarah to open — not stored, just the prompt seed
        $seed = [['role' => 'user', 'parts' => [['text' => 'Please begin the interview session.']]]];

        $opening = $this->callGemini($systemPrompt, $seed);

        InterviewMessage::create([
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => $opening,
        ]);

        return ['session' => $session, 'message' => $opening];
    }

    /**
     * Append a user message, call Gemini with full history, save and return the AI reply.
     */
    public function sendMessage(InterviewSession $session, string $userContent): string
    {
        // Save user turn first
        InterviewMessage::create([
            'session_id' => $session->id,
            'role'       => 'user',
            'content'    => $userContent,
        ]);

        $session->load('cv.sections');
        $systemPrompt = $this->buildSystemPrompt($session->cv, $session->job_target);

        $contents = $this->buildRecentConversationContents($session);

        $reply = $this->callGemini($systemPrompt, $contents);

        InterviewMessage::create([
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => $reply,
        ]);

        return $reply;
    }

    /**
     * Build a CV-specific system prompt with the Ms. Sarah HRD persona.
     * At least 60% of generated questions should reference specific CV content.
     */
    public function buildSystemPrompt(Cv $cv, string $jobTarget): string
    {
        $resumeText = $this->flattenSections($cv);

        return <<<PROMPT
You are Ms. Sarah, an experienced HRD professional at a reputable company conducting a job interview.
The candidate is applying for: {$jobTarget}

Here is the candidate's CV, which you must use as the basis for your questions:

{$resumeText}

Interview guidelines:
- Ask questions that DIRECTLY reference content in the CV above (company names, job titles, projects, dates, technologies, or specific achievements listed).
- At least 60% of questions must contain specific references from the CV.
- Do not ask generic questions that could be directed at anyone.
- Conduct the interview naturally: 1-2 questions per turn, wait for answers before continuing.
- Use professional yet warm English. Conversational but formal tone.
- Explore STAR topics (Situation, Task, Action, Result) in each of the candidate's answers.
- Do not score or evaluate during the session — save your assessment for the end.
PROMPT;
    }

    /**
     * Generate a structured STAR-method feedback report for a completed session.
     * Calls Gemini in JSON mode and persists the result to interview_feedback.
     *
     * @throws \Exception on API failure or invalid JSON.
     */
    public function generateFeedback(InterviewSession $session): InterviewFeedback
    {
        $session->load(['messages', 'cv.sections']);

        $transcript = $this->buildTranscript($session->messages);
        $prompt     = $this->buildFeedbackPrompt($session->job_target, $transcript);

        $data = $this->callGeminiJson($prompt, 45);

        $score = max(0, min(100, (int) ($data['overall_score'] ?? 0)));
        $badge = match (true) {
            $score >= 75 => 'ready',
            $score >= 50 => 'almost_ready',
            default      => 'needs_practice',
        };

        return InterviewFeedback::create([
            'session_id'       => $session->id,
            'question_scores'  => $data['question_scores'] ?? [],
            'missing_keywords' => $data['missing_keywords'] ?? [],
            'overall_score'    => $score,
            'readiness_badge'  => $badge,
        ]);
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Convert CV sections to structured plain text for prompt injection.
     * Adapted from ManuscriptAtsController::flattenCv().
     */
    private function flattenSections(Cv $cv): string
    {
        $lines = [];

        foreach ($cv->sections->sortBy('order') as $section) {
            if ($section->type === 'target_job') continue;

            $content = $section->content;
            if (empty($content)) continue;

            $lines[] = '=== ' . strtoupper($section->title) . ' ===';

            if (isset($content[0]) && is_array($content[0])) {
                // List-based sections (work_experience, education, skills, etc.)
                foreach ($content as $item) {
                    if (is_array($item)) {
                        $parts = array_filter(array_map('strval', array_values($item)));
                        $lines[] = '• ' . implode(' | ', $parts);
                    }
                }
            } else {
                // Flat object (personal_info)
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

    /**
     * Multi-turn Gemini call. Uses natural language output (no JSON mode).
     * $messages is the full contents[] array including all prior turns.
     *
     * @throws \Exception on API failure or empty response.
     */
    private function callGemini(string $systemPrompt, array $messages, int $timeout = 30): string
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }

        $response = Http::timeout($timeout)->connectTimeout(5)->post(
            self::GEMINI_URL . "?key={$apiKey}",
            [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents'           => $messages,
                'generationConfig'   => ['maxOutputTokens' => 600, 'temperature' => 0.7],
            ]
        );

        if ($response->failed()) {
            Log::error('InterviewService Gemini API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('AI service failed with status ' . $response->status());
        }

        $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$text) {
            throw new \Exception('Empty response from AI service');
        }

        return trim($text);
    }

    /**
     * Build the single-turn feedback analysis prompt.
     */
    private function buildFeedbackPrompt(string $jobTarget, string $transcript): string
    {
        return <<<PROMPT
You are an expert interview assessor. Analyze the following mock interview conversation and provide structured feedback.

POSITION APPLIED FOR: {$jobTarget}

INTERVIEW CONVERSATION:
{$transcript}

Evaluate the candidate's answers using the STAR method and return a JSON object with the following structure (JSON ONLY, no other text):
{
  "overall_score": (integer 0-100, average overall performance),
  "readiness_badge": ("ready" if score >= 75, "almost_ready" if >= 50, "needs_practice" if < 50),
  "missing_keywords": (array of strings: important skills/keywords the candidate failed to mention),
  "question_scores": [
    {
      "question": (string: the HRD's question),
      "answer_summary": (string: 1-2 sentence summary of the candidate's answer),
      "star_scores": {
        "situation": (integer 0-100),
        "task": (integer 0-100),
        "action": (integer 0-100),
        "result": (integer 0-100)
      },
      "feedback": (string: specific, actionable feedback for this answer)
    }
  ]
}

Only include questions that were actually answered by the candidate in question_scores.
PROMPT;
    }

    /**
     * Format session messages as a readable transcript string.
     */
    private function buildTranscript(Collection $messages): string
    {
        $lines = [];

        foreach ($messages as $msg) {
            $prefix  = $msg->role === 'assistant' ? 'Ms. Sarah (HRD)' : 'Candidate';
            $lines[] = "{$prefix}: {$msg->content}";
        }

        return implode("\n\n", $lines);
    }

    public function buildRecentConversationContents(InterviewSession $session): array
    {
        $messages = $session->messages()
            ->reorder()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(self::RECENT_MESSAGE_LIMIT)
            ->get()
            ->reverse()
            ->values();

        $contents = [['role' => 'user', 'parts' => [['text' => 'Please begin the interview session.']]]];

        foreach ($messages as $msg) {
            $contents[] = [
                'role'  => $msg->role === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg->content]],
            ];
        }

        return $contents;
    }

    /**
     * Streaming Gemini call via SSE. Calls $onToken for each text token as it arrives.
     * Returns the fully assembled response string.
     *
     * Uses Http::withOptions(['stream' => true]) so Http::fake() intercepts in tests.
     *
     * @throws \Exception on API failure.
     */
    public function callGeminiStreaming(string $systemPrompt, array $messages, callable $onToken): string
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }

        $response = Http::withOptions(['stream' => true])
            ->timeout(60)
            ->connectTimeout(5)
            ->post(
                self::GEMINI_STREAM_URL . "?key={$apiKey}&alt=sse",
                [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents'           => $messages,
                    'generationConfig'   => ['maxOutputTokens' => 600, 'temperature' => 0.7],
                ]
            );

        if ($response->failed()) {
            throw new \Exception('AI streaming service failed with status ' . $response->status());
        }

        $body     = $response->toPsrResponse()->getBody();
        $fullText = '';
        $buffer   = '';

        while (!$body->eof()) {
            $chunk = $body->read(256);
            if (!$chunk) break;
            $buffer .= $chunk;

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line   = rtrim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if (str_starts_with($line, 'data: ')) {
                    $json = substr($line, 6);
                    if ($json === '[DONE]') break 2;
                    $payload = json_decode($json, true);
                    $token   = $payload['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if ($token) {
                        $fullText .= $token;
                        $onToken($token);
                    }
                }
            }
        }

        return $fullText;
    }

    /**
     * Single-turn Gemini call with JSON response mode.
     *
     * @throws \Exception on API failure, empty response, or invalid JSON.
     */
    private function callGeminiJson(string $prompt, int $timeout = 45): array
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }

        $response = Http::timeout($timeout)->connectTimeout(5)->post(
            self::GEMINI_URL . "?key={$apiKey}",
            [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                    'maxOutputTokens'    => 2000,
                    'temperature'        => 0.3,
                ],
            ]
        );

        if ($response->failed()) {
            Log::error('InterviewService feedback Gemini API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('Feedback AI service failed with status ' . $response->status());
        }

        $content = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$content) {
            throw new \Exception('Empty feedback response from AI service');
        }

        $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
        $decoded = json_decode($content, true);

        if ($decoded === null) {
            throw new \Exception('Invalid JSON feedback from AI service');
        }

        return $decoded;
    }
}
