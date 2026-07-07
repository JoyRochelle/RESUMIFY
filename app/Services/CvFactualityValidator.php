<?php

namespace App\Services;

class CvFactualityValidator
{
    /**
     * Common sentence-starting / resume action words that are capitalized
     * at the start of a bullet but are not proper nouns.
     */
    private const IGNORED_WORDS = [
        'the', 'a', 'an', 'and', 'or', 'but', 'for', 'nor', 'so', 'yet',
        'in', 'on', 'at', 'by', 'to', 'of', 'from', 'with', 'as', 'i', 'my',
        'this', 'these', 'those', 'responsible', 'focused',
        'led', 'managed', 'developed', 'built', 'created', 'designed',
        'implemented', 'improved', 'increased', 'reduced', 'achieved',
        'collaborated', 'coordinated', 'directed', 'oversaw', 'spearheaded',
        'streamlined', 'delivered', 'drove', 'established', 'enhanced',
        'executed', 'facilitated', 'generated', 'handled', 'initiated',
        'launched', 'maintained', 'optimized', 'organized', 'performed',
        'planned', 'produced', 'provided', 'resolved', 'reviewed',
        'supervised', 'supported', 'trained', 'utilized', 'grew',
        'kept', 'partnered',
    ];

    /**
     * Compare AI-adapted CV sections against the user's original sections and
     * flag numbers/proper nouns that appear in the output but not the source.
     * Heuristic first pass — surfaces candidates for the user to double-check,
     * never blocks or strips content.
     */
    public function validate(array $sourceSections, array $adaptedSections): array
    {
        $sourceText = $this->flattenText($sourceSections);
        $outputText = $this->flattenText($adaptedSections);

        $newNumbers = array_values(array_diff(
            $this->extractNumbers($outputText),
            $this->extractNumbers($sourceText),
        ));

        $sourceEntities = $this->extractEntities($sourceText);
        $newEntities = array_values(array_filter(
            $this->extractEntities($outputText),
            fn (string $candidate) => !$this->containsCaseInsensitive($sourceEntities, $candidate),
        ));

        return [
            'flagged' => !empty($newNumbers) || !empty($newEntities),
            'numbers' => $newNumbers,
            'entities' => $newEntities,
        ];
    }

    private function flattenText(array $data): string
    {
        $parts = [];

        array_walk_recursive($data, function ($value) use (&$parts): void {
            if (is_string($value)) {
                $parts[] = $value;
            }
        });

        return implode("\n", $parts);
    }

    private function extractNumbers(string $text): array
    {
        preg_match_all('/\b\d[\d,]*(?:\.\d+)?%?\b/', $text, $matches);

        $numbers = array_map(
            fn (string $number) => rtrim(str_replace(',', '', $number), '.'),
            $matches[0],
        );

        return array_values(array_unique($numbers));
    }

    private function extractEntities(string $text): array
    {
        preg_match_all('/\b[A-Z][A-Za-z0-9&\']*(?:[ \t]+[A-Z][A-Za-z0-9&\']*)*\b/', $text, $matches);

        $entities = [];
        foreach ($matches[0] as $candidate) {
            $isMultiWord = str_contains($candidate, ' ');
            $isMeaningfulSingleWord = !$isMultiWord
                && strlen($candidate) >= 3
                && !in_array(strtolower($candidate), self::IGNORED_WORDS, true);

            if ($isMultiWord || $isMeaningfulSingleWord) {
                $entities[] = $candidate;
            }
        }

        return array_values(array_unique($entities));
    }

    private function containsCaseInsensitive(array $haystack, string $needle): bool
    {
        foreach ($haystack as $item) {
            if (strcasecmp($item, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}
