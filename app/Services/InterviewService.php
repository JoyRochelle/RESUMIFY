<?php

namespace App\Services;

use App\Domain\Ai\Data\InterviewFeedbackResponse;
use App\Exceptions\InvalidAiProviderResponseException;
use App\Models\Cv;
use App\Models\InterviewFeedback;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
use App\Support\Concerns\TracksGeminiUsage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InterviewService
{
    use TracksGeminiUsage;

    private const GEMINI_URL        = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    private const GEMINI_STREAM_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:streamGenerateContent';
    public const RECENT_MESSAGE_LIMIT = 20;

    /**
     * Ceiling for the conversational (non-JSON) Gemini calls. Ms. Sarah is
     * instructed to ask elaborate, CV-referencing questions; 30s/60s proved
     * too tight and aborted otherwise-successful long replies.
     */
    private const GEMINI_CONVERSATION_TIMEOUT_SECONDS = 90;

    /**
     * Ceiling for the end-of-session feedback call. Analyzing a full
     * transcript is the most reasoning-heavy call in the system, yet it
     * previously had the tightest timeout (45s) of any Gemini call here.
     */
    private const GEMINI_FEEDBACK_TIMEOUT_SECONDS = 90;

    /**
     * Create an interview session and ask Bu Sari's opening question.
     *
     * @return array{session: InterviewSession, message: string, usage: \App\Support\GeminiUsage}
     */
    public function startSession(User $user, Cv $cv, string $jobTarget): array
    {
        $this->resetUsage();
        $cv->load('sections');

        $systemPrompt = $this->buildSystemPrompt($cv, $jobTarget);

        // Trigger Ms. Sarah to open — not stored, just the prompt seed
        $seed = [['role' => 'user', 'parts' => [['text' => 'Please begin the interview session.']]]];

        // Call the AI *before* persisting anything. If Gemini fails, this throws
        // and no session row is left behind — otherwise every failed attempt
        // would create an empty, orphaned "active" session the user could spam.
        $opening = $this->callGemini($systemPrompt, $seed);

        // Persist the session and its opening message atomically only on success.
        return DB::transaction(function () use ($user, $cv, $jobTarget, $opening) {
            $session = InterviewSession::create([
                'user_id'    => $user->id,
                'resume_id'  => $cv->id,
                'job_target' => $jobTarget,
                'status'     => 'active',
                'started_at' => now(),
            ]);

            InterviewMessage::create([
                'session_id' => $session->id,
                'role'       => 'assistant',
                'content'    => $opening,
            ]);

            return ['session' => $session, 'message' => $opening, 'usage' => $this->lastUsage()];
        });
    }

    /**
     * Append a user message, call Gemini with full history, save and return the AI reply.
     */
    public function sendMessage(InterviewSession $session, string $userContent): string
    {
        $this->resetUsage();

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
        $this->resetUsage();
        $session->load(['messages', 'cv.sections']);

        $transcript = $this->buildTranscript($session->messages);
        $prompt     = $this->buildFeedbackPrompt($session->job_target, $transcript);

        $data = $this->callGeminiJson($prompt, self::GEMINI_FEEDBACK_TIMEOUT_SECONDS, [InterviewFeedbackResponse::class, 'fromProvider']);

        return InterviewFeedback::create([
            'session_id'       => $session->id,
            'question_scores'  => $data['question_scores'],
            'missing_keywords' => $data['missing_keywords'],
            'overall_score'    => $data['overall_score'],
            'readiness_badge'  => $data['readiness_badge'],
        ]);
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Convert CV sections to structured plain text for prompt injection.
     * Adapted from User\Resume\ManuscriptAtsController::flattenCv().
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
    private function callGemini(string $systemPrompt, array $messages, int $timeout = self::GEMINI_CONVERSATION_TIMEOUT_SECONDS): string
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
                'generationConfig'   => self::conversationGenerationConfig(),
            ]
        );

        if ($response->failed()) {
            Log::error('InterviewService Gemini API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('AI service failed with status ' . $response->status());
        }

        $json = $response->json();
        $this->recordUsage($json);

        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
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
        $this->resetUsage();

        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }

        $response = Http::withOptions(['stream' => true])
            ->timeout(self::GEMINI_CONVERSATION_TIMEOUT_SECONDS)
            ->connectTimeout(5)
            ->post(
                self::GEMINI_STREAM_URL . "?key={$apiKey}&alt=sse",
                [
                    'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                    'contents'           => $messages,
                    'generationConfig'   => self::conversationGenerationConfig(),
                ]
            );

        if ($response->failed()) {
            throw new \Exception('AI streaming service failed with status ' . $response->status());
        }

        $body      = $response->toPsrResponse()->getBody();
        $fullText  = '';
        $buffer    = '';
        $usageJson = null;

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
                    // usageMetadata rides the final SSE chunk and is cumulative,
                    // so keep the last one seen rather than summing per chunk.
                    if (isset($payload['usageMetadata'])) {
                        $usageJson = $payload;
                    }
                    $token   = $payload['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    if ($token) {
                        $fullText .= $token;
                        $onToken($token);
                    }
                }
            }
        }

        $this->recordUsage($usageJson);

        return $fullText;
    }

    /**
     * Single-turn Gemini call with JSON response mode.
     *
     * @throws \Exception on API failure, empty response, or invalid JSON.
     */
    private function callGeminiJson(string $prompt, int $timeout = self::GEMINI_FEEDBACK_TIMEOUT_SECONDS, ?callable $validator = null): array
    {
        $content = $this->requestGeminiJsonText($prompt, self::feedbackSchema(), $timeout);

        try {
            return $this->decodeAndValidateJson($content, $validator);
        } catch (InvalidAiProviderResponseException $e) {
            Log::warning('InterviewService feedback Gemini JSON invalid; retrying repair once', [
                'error' => $e->getMessage(),
            ]);

            $repairContent = $this->requestGeminiJsonText(
                $this->buildRepairPrompt($prompt, $content, $e->getMessage()),
                self::feedbackSchema(),
                $timeout,
            );

            return $this->decodeAndValidateJson($repairContent, $validator);
        }
    }

    private function requestGeminiJsonText(string $prompt, array $schema, int $timeout = self::GEMINI_FEEDBACK_TIMEOUT_SECONDS): string
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }

        $response = Http::timeout($timeout)->connectTimeout(5)->post(
            self::GEMINI_URL . "?key={$apiKey}",
            [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => self::jsonGenerationConfig($schema),
            ]
        );

        if ($response->failed()) {
            Log::error('InterviewService feedback Gemini API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('Feedback AI service failed with status ' . $response->status());
        }

        $json = $response->json();
        $this->recordUsage($json);

        $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$content) {
            throw new \Exception('Empty feedback response from AI service');
        }

        return $content;
    }

    private function decodeAndValidateJson(string $content, ?callable $validator = null): array
    {
        $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
        $decoded = json_decode($content, true);

        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidAiProviderResponseException('Invalid JSON feedback from AI service');
        }

        if ($validator) {
            return $validator($decoded);
        }

        return $decoded;
    }

    private function buildRepairPrompt(string $originalPrompt, string $invalidJson, string $error): string
    {
        return <<<PROMPT
The previous response did not satisfy the required JSON contract.

Validation error:
{$error}

Original task:
{$originalPrompt}

Invalid response:
{$invalidJson}

Return ONLY corrected valid JSON that satisfies the original task and schema. Do not include markdown or explanation.
PROMPT;
    }

    /**
     * Generation config for the conversational (non-JSON) Ms. Sarah calls.
     * thinkingBudget is set to 0 because this persona needs a direct
     * conversational reply, not hidden chain-of-thought — otherwise Gemini
     * 2.5 Flash can spend the whole token/time budget on reasoning before
     * emitting any visible text, especially for longer, elaborate replies.
     */
    private static function conversationGenerationConfig(): array
    {
        return [
            'maxOutputTokens' => 600,
            'temperature'     => 0.7,
            'thinkingConfig'  => ['thinkingBudget' => 0],
        ];
    }

    /**
     * thinkingBudget is set to 0 for the same reason as
     * conversationGenerationConfig(): analyzing a full interview transcript
     * for STAR-method feedback is the most reasoning-heavy call in this
     * service, so it was the most exposed to hidden thinking silently
     * consuming the entire token/time budget before any JSON was emitted.
     */
    private static function jsonGenerationConfig(array $schema): array
    {
        return [
            'response_mime_type' => 'application/json',
            'maxOutputTokens' => 2000,
            'temperature' => 0.3,
            'response_schema' => $schema,
            'thinkingConfig' => ['thinkingBudget' => 0],
        ];
    }

    /**
     * Structural schema only — no minimum/maximum, maxLength, or maxItems.
     * Gemini rejects response_schemas whose combined constraints have "too
     * many states for serving" (400 INVALID_ARGUMENT, observed 2026-07-06),
     * and every one of those bounds is already enforced app-side by
     * InterviewFeedbackResponse::fromProvider with a repair retry.
     */
    private static function feedbackSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'overall_score' => ['type' => 'integer'],
                'readiness_badge' => ['type' => 'string', 'enum' => ['ready', 'almost_ready', 'needs_practice']],
                'missing_keywords' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'question_scores' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'question' => ['type' => 'string'],
                            'answer_summary' => ['type' => 'string'],
                            'star_scores' => [
                                'type' => 'object',
                                'properties' => [
                                    'situation' => ['type' => 'integer'],
                                    'task' => ['type' => 'integer'],
                                    'action' => ['type' => 'integer'],
                                    'result' => ['type' => 'integer'],
                                ],
                                'required' => ['situation', 'task', 'action', 'result'],
                            ],
                            'feedback' => ['type' => 'string'],
                        ],
                        'required' => ['question', 'answer_summary', 'star_scores', 'feedback'],
                    ],
                ],
            ],
            'required' => ['overall_score', 'readiness_badge', 'missing_keywords', 'question_scores'],
        ];
    }
}
