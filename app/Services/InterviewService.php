<?php

namespace App\Services;

use App\Models\Cv;
use App\Models\InterviewMessage;
use App\Models\InterviewSession;
use App\Models\User;
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
}
