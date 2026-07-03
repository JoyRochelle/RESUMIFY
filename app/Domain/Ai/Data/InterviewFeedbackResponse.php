<?php

namespace App\Domain\Ai\Data;

class InterviewFeedbackResponse extends AiProviderResponseValidator
{
    /**
     * @param array<int|string, mixed> $data
     * @return array<string, mixed>
     */
    public static function fromProvider(array $data): array
    {
        self::assertExactKeys($data, [
            'overall_score',
            'readiness_badge',
            'missing_keywords',
            'question_scores',
        ], 'interview_feedback');

        return [
            'overall_score' => self::assertIntRange($data['overall_score'], 0, 100, 'overall_score'),
            'readiness_badge' => self::assertEnum($data['readiness_badge'], ['ready', 'almost_ready', 'needs_practice'], 'readiness_badge'),
            'missing_keywords' => self::assertStringList($data['missing_keywords'], 'missing_keywords', 30, 100),
            'question_scores' => self::questionScores($data['question_scores']),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function questionScores(mixed $value): array
    {
        $items = self::assertList($value, 'question_scores', 30);

        return array_map(function (mixed $item, int $index): array {
            $item = self::assertObject($item, "question_scores.{$index}");
            self::assertExactKeys($item, [
                'question',
                'answer_summary',
                'star_scores',
                'feedback',
            ], "question_scores.{$index}");

            $starScores = self::assertObject($item['star_scores'], "question_scores.{$index}.star_scores");
            self::assertExactKeys($starScores, ['situation', 'task', 'action', 'result'], "question_scores.{$index}.star_scores");

            return [
                'question' => self::assertString($item['question'], "question_scores.{$index}.question", 500),
                'answer_summary' => self::assertString($item['answer_summary'], "question_scores.{$index}.answer_summary", 700),
                'star_scores' => [
                    'situation' => self::assertIntRange($starScores['situation'], 0, 100, "question_scores.{$index}.star_scores.situation"),
                    'task' => self::assertIntRange($starScores['task'], 0, 100, "question_scores.{$index}.star_scores.task"),
                    'action' => self::assertIntRange($starScores['action'], 0, 100, "question_scores.{$index}.star_scores.action"),
                    'result' => self::assertIntRange($starScores['result'], 0, 100, "question_scores.{$index}.star_scores.result"),
                ],
                'feedback' => self::assertString($item['feedback'], "question_scores.{$index}.feedback", 1000),
            ];
        }, $items, array_keys($items));
    }
}
