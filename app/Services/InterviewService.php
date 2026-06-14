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
    private const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

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

        // Trigger Bu Sari to open — not stored, just the prompt seed
        $seed = [['role' => 'user', 'parts' => [['text' => 'Silakan mulai sesi wawancara.']]]];

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

        // Build full conversation history for Gemini (seed + all saved turns)
        $contents = [['role' => 'user', 'parts' => [['text' => 'Silakan mulai sesi wawancara.']]]];
        foreach ($session->messages as $msg) {
            $contents[] = [
                'role'  => $msg->role === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg->content]],
            ];
        }

        $reply = $this->callGemini($systemPrompt, $contents);

        InterviewMessage::create([
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => $reply,
        ]);

        return $reply;
    }

    /**
     * Build a CV-specific system prompt with the Bu Sari HRD persona.
     * At least 60% of generated questions should reference specific CV content.
     */
    public function buildSystemPrompt(Cv $cv, string $jobTarget): string
    {
        $resumeText = $this->flattenSections($cv);

        return <<<PROMPT
Kamu adalah Bu Sari, seorang HRD berpengalaman di perusahaan profesional yang sedang mewawancarai kandidat.
Kandidat melamar posisi: {$jobTarget}

Berikut adalah CV kandidat yang harus kamu gunakan sebagai dasar pertanyaan:

{$resumeText}

Panduan wawancara:
- Ajukan pertanyaan yang LANGSUNG merujuk pada isi CV di atas (nama perusahaan, jabatan, proyek, tanggal, teknologi, atau pencapaian spesifik yang tercantum).
- Minimal 60% pertanyaan harus mengandung referensi spesifik dari CV.
- Jangan ajukan pertanyaan generik yang bisa ditujukan ke siapa saja.
- Lakukan wawancara secara alami: 1–2 pertanyaan per giliran, tunggu jawaban sebelum melanjutkan.
- Gunakan bahasa Indonesia profesional namun hangat. Campuran Bahasa Indonesia dan Bahasa Inggris diperbolehkan.
- Eksplorasi topik STAR (Situation, Task, Action, Result) pada setiap jawaban kandidat.
- Jangan beri skor atau evaluasi selama sesi berlangsung — simpan penilaian untuk akhir sesi.
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
Kamu adalah penilai wawancara kerja yang ahli. Analisis percakapan wawancara berikut dan berikan penilaian terstruktur.

POSISI YANG DILAMAR: {$jobTarget}

PERCAKAPAN WAWANCARA:
{$transcript}

Evaluasi jawaban kandidat menggunakan metode STAR dan kembalikan objek JSON dengan struktur berikut (HANYA JSON, tanpa teks lain):
{
  "overall_score": (integer 0-100, rata-rata performa keseluruhan),
  "readiness_badge": ("ready" jika skor >= 75, "almost_ready" jika >= 50, "needs_practice" jika < 50),
  "missing_keywords": (array string: skill/kata kunci penting yang tidak disebutkan kandidat),
  "question_scores": [
    {
      "question": (string: pertanyaan yang diajukan HRD),
      "answer_summary": (string: ringkasan 1-2 kalimat jawaban kandidat),
      "star_scores": {
        "situation": (integer 0-100),
        "task": (integer 0-100),
        "action": (integer 0-100),
        "result": (integer 0-100)
      },
      "feedback": (string: umpan balik spesifik dan dapat ditindaklanjuti untuk jawaban ini)
    }
  ]
}

Hanya sertakan pertanyaan yang benar-benar dijawab oleh kandidat dalam question_scores.
PROMPT;
    }

    /**
     * Format session messages as a readable transcript string.
     */
    private function buildTranscript(Collection $messages): string
    {
        $lines = [];

        foreach ($messages as $msg) {
            $prefix  = $msg->role === 'assistant' ? 'Bu Sari (HRD)' : 'Kandidat';
            $lines[] = "{$prefix}: {$msg->content}";
        }

        return implode("\n\n", $lines);
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
