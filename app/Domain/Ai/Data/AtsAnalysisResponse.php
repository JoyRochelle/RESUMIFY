<?php

namespace App\Domain\Ai\Data;

class AtsAnalysisResponse extends AiProviderResponseValidator
{
    /**
     * @param array<int|string, mixed> $data
     * @return array<string, mixed>
     */
    public static function fromProvider(array $data): array
    {
        self::assertExactKeys($data, [
            'score',
            'rating',
            'keyword_score',
            'matched',
            'missing',
            'section_breakdown',
            'action_verbs',
            'missing_verbs',
            'has_numbers',
            'length_tip',
            'insights',
        ], 'ats_analysis');

        $rating = self::assertObject($data['rating'], 'rating');
        self::assertExactKeys($rating, ['label', 'sublabel', 'color'], 'rating');

        return [
            'score' => self::assertIntRange($data['score'], 0, 100, 'score'),
            'rating' => [
                'label' => self::assertEnum($rating['label'], ['Excellent', 'Very Good', 'Fair', 'Weak'], 'rating.label'),
                'sublabel' => self::assertEnum($rating['sublabel'], ['ATS-Ready', 'Needs Minor Polish', 'Room to Improve', 'Needs Rework'], 'rating.sublabel'),
                'color' => self::assertEnum($rating['color'], ['success', 'warning', 'danger'], 'rating.color'),
            ],
            'keyword_score' => self::assertIntRange($data['keyword_score'], 0, 100, 'keyword_score'),
            'matched' => self::assertStringList($data['matched'], 'matched', 15),
            'missing' => self::missingKeywords($data['missing']),
            'section_breakdown' => self::sectionBreakdown($data['section_breakdown']),
            'action_verbs' => self::assertStringList($data['action_verbs'], 'action_verbs', 20),
            'missing_verbs' => self::assertStringList($data['missing_verbs'], 'missing_verbs', 10),
            'has_numbers' => self::assertBool($data['has_numbers'], 'has_numbers'),
            'length_tip' => self::assertString($data['length_tip'], 'length_tip', 500),
            'insights' => self::insights($data['insights']),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function missingKeywords(mixed $value): array
    {
        $items = self::assertList($value, 'missing', 20);

        return array_map(function (mixed $item, int $index): array {
            $item = self::assertObject($item, "missing.{$index}");
            self::assertExactKeys($item, ['keyword', 'context'], "missing.{$index}");

            return [
                'keyword' => self::assertString($item['keyword'], "missing.{$index}.keyword", 100),
                'context' => self::assertString($item['context'], "missing.{$index}.context", 500),
            ];
        }, $items, array_keys($items));
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function sectionBreakdown(mixed $value): array
    {
        $items = self::assertList($value, 'section_breakdown', 12);

        return array_map(function (mixed $item, int $index): array {
            $item = self::assertObject($item, "section_breakdown.{$index}");
            self::assertExactKeys($item, ['section', 'strength', 'feedback'], "section_breakdown.{$index}");

            return [
                'section' => self::assertString($item['section'], "section_breakdown.{$index}.section", 100),
                'strength' => self::assertEnum($item['strength'], ['Strong', 'Adequate', 'Weak'], "section_breakdown.{$index}.strength"),
                'feedback' => self::assertString($item['feedback'], "section_breakdown.{$index}.feedback", 700),
            ];
        }, $items, array_keys($items));
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function insights(mixed $value): array
    {
        $items = self::assertList($value, 'insights', 4);

        if (count($items) !== 4) {
            self::invalid('insights must contain exactly 4 items.');
        }

        return array_map(function (mixed $item, int $index): array {
            $item = self::assertObject($item, "insights.{$index}");
            self::assertExactKeys($item, ['title', 'body'], "insights.{$index}");

            return [
                'title' => self::assertString($item['title'], "insights.{$index}.title", 100),
                'body' => self::assertString($item['body'], "insights.{$index}.body", 700),
            ];
        }, $items, array_keys($items));
    }
}
