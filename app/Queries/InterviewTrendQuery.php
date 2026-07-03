<?php

namespace App\Queries;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InterviewTrendQuery
{
    /**
     * Calculate visible score trends for a collection of interview sessions.
     * Returns an array mapping session_id => score_difference.
     */
    public function getTrendsForSessions(Collection $sessions, string $userId): array
    {
        $trends = [];
        $sessionsWithFeedback = $sessions->filter(fn($s) => $s->feedback !== null);
        
        if ($sessionsWithFeedback->isEmpty()) {
            return $trends;
        }

        $sessionIds = $sessionsWithFeedback->pluck('id')->toArray();

        $previousScores = DB::table('interview_sessions as t')
            ->select('t.id as session_id')
            ->selectSub(
                DB::table('interview_sessions as s')
                    ->join('interview_feedback as f', 's.id', '=', 'f.session_id')
                    ->where('s.user_id', $userId)
                    ->whereColumn('s.started_at', '<', 't.started_at')
                    ->orderByDesc('s.started_at')
                    ->select('f.overall_score')
                    ->limit(1),
                'previous_score'
            )
            ->whereIn('t.id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        foreach ($sessionsWithFeedback as $session) {
            $prev = $previousScores->get($session->id)?->previous_score;
            if ($prev !== null) {
                $trends[$session->id] = $session->feedback->overall_score - (int) $prev;
            }
        }

        return $trends;
    }
}
