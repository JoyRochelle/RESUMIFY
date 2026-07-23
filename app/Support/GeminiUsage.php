<?php

namespace App\Support;

/**
 * Token accounting for a Gemini call, parsed from the `usageMetadata` block
 * every generateContent / streamGenerateContent response carries. Previously
 * this was discarded, so every ai_usage_logs row stored 0 tokens / $0 cost and
 * the admin panel always reported zero AI usage.
 */
class GeminiUsage
{
    public function __construct(
        public readonly int $promptTokens = 0,
        public readonly int $candidatesTokens = 0,
        public readonly int $totalTokens = 0,
    ) {}

    /**
     * Extract usage from a decoded Gemini response body. Missing/faked
     * responses (no usageMetadata) degrade to a zero-usage instance.
     */
    public static function fromResponse(?array $json): self
    {
        $meta = $json['usageMetadata'] ?? [];

        $prompt     = (int) ($meta['promptTokenCount'] ?? 0);
        $candidates = (int) ($meta['candidatesTokenCount'] ?? 0);
        $total      = (int) ($meta['totalTokenCount'] ?? ($prompt + $candidates));

        return new self($prompt, $candidates, $total);
    }

    /**
     * Combine two usage tallies — used when one logical operation makes more
     * than one Gemini call (e.g. a feedback JSON repair retry).
     */
    public function plus(self $other): self
    {
        return new self(
            $this->promptTokens + $other->promptTokens,
            $this->candidatesTokens + $other->candidatesTokens,
            $this->totalTokens + $other->totalTokens,
        );
    }

    /**
     * Estimated USD cost using the per-million-token rates in config. Input and
     * output tokens are priced separately, matching Gemini's published pricing.
     */
    public function costUsd(): float
    {
        $inputRate  = (float) config('services.gemini.pricing.input_per_million', 0);
        $outputRate = (float) config('services.gemini.pricing.output_per_million', 0);

        return ($this->promptTokens / 1_000_000) * $inputRate
             + ($this->candidatesTokens / 1_000_000) * $outputRate;
    }
}
