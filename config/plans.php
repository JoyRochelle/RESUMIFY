<?php

return [
    'resume_limits' => [
        'basic' => (int) env('RESUME_LIMIT_BASIC', 1),
        'premium' => env('RESUME_LIMIT_PREMIUM') !== null ? (int) env('RESUME_LIMIT_PREMIUM') : null,
        'admin' => null,
    ],

    /*
     * PDF export is deliberately absent: every plan may download its resume.
     * What stays paid is the premium *design* — see the template check in
     * ResumeExportController::downloadPdf().
     */
    'premium_features' => [
        'ats_analyze',
        'premium_templates',
    ],

    /*
     * How many times a Basic user may run a premium feature before the
     * upgrade wall goes up. Usage is counted from records the user cannot
     * delete, so the allowance cannot be reset by clearing history.
     */
    'trials' => [
        'ats_analyze' => (int) env('TRIAL_ATS_BASIC', 3),
        'interview' => (int) env('TRIAL_INTERVIEW_BASIC', 3),
    ],
];
