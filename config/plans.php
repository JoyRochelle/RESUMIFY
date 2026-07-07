<?php

return [
    'resume_limits' => [
        'basic' => (int) env('RESUME_LIMIT_BASIC', 1),
        'premium' => env('RESUME_LIMIT_PREMIUM') !== null ? (int) env('RESUME_LIMIT_PREMIUM') : null,
        'admin' => null,
    ],

    'premium_features' => [
        'ats_analyze',
        'premium_templates',
        'pdf_export',
    ],
];
