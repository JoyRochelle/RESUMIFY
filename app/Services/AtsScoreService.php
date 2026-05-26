<?php

namespace App\Services;

use App\Models\Cv;

class AtsScoreService {
    /**
     * Calculate basic ATS score based on keyword match.
     *
     * @param Cv $cv
     * @return int
     */
    public static function calculate(Cv $cv): int
    {
        $jobTargetSection = $cv->sections()->where('type', 'target_job')->first();
        
        $jobTitle = $cv->job_target ?? ($jobTargetSection ? ($jobTargetSection->content['job_title'] ?? '') : '');
        $jobDesc = $jobTargetSection ? ($jobTargetSection->content['job_description'] ?? '') : '';
        $jobText = $jobTitle . ' ' . $jobDesc;
        
        if (empty(trim($jobText))) {
            return 0;
        }

        $personalInfo = $cv->sections()->where('type', 'personal_info')->first();
        $summary = $personalInfo ? ($personalInfo->content['summary'] ?? '') : '';

        $resumeText = '';
        foreach ($cv->sections as $section) {
            if ($section->type === 'target_job') {
                continue;
            }
            $resumeText .= json_encode($section->content) . ' ';
        }

        $jobWords = self::getKeywords($jobText);
        $resumeWords = self::getKeywords($resumeText);
        $summaryWords = self::getKeywords($summary);

        if (empty($jobWords)) {
            return 0;
        }

        $resumeIntersect = array_intersect($jobWords, $resumeWords);
        $summaryIntersect = array_intersect($jobWords, $summaryWords);
        $coreScore = (count($resumeIntersect) / min(20, count($jobWords))) * 100;
        $summaryBonus = count($summaryIntersect) * 5;

        $score = $coreScore + $summaryBonus;
        
        return (int) min(100, round($score));
    }

    private static function getKeywords(string $text): array
    {
        $text = strtolower(strip_tags($text));
        $keywords = explode(' ', $text);
        $stopwords = ['and', 'or', 'the', 'a', 'an', 'in', 'on', 'at', 'for', 'to', 'of', 'with', 'is', 'are', 'this', 'that', 'it'];
        
        $validKeywords = array_filter($keywords, function ($word) use ($stopwords) {
            $word = preg_replace('/[^a-z0-9]/', '', $word);
            return strlen($word) >= 3 && !in_array($word, $stopwords);
        });
        
        return array_unique(array_values(array_map(function($word) {
            return preg_replace('/[^a-z0-9]/', '', $word);
        }, $validKeywords)));
    }
}
