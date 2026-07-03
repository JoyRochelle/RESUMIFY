<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public const QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';
    public const PREMIUM_REQUIRED = 'PREMIUM_REQUIRED';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const FORBIDDEN = 'FORBIDDEN';
    public const AI_PROVIDER_TIMEOUT = 'AI_PROVIDER_TIMEOUT';
    public const AI_PROVIDER_INVALID_RESPONSE = 'AI_PROVIDER_INVALID_RESPONSE';

    public static function success(mixed $data = null, array $meta = [], int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'data' => $data,
        ];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        if (is_array($data)) {
            $payload = array_merge($data, $payload);
        }

        return response()->json($payload, $status);
    }

    public static function error(
        string $code,
        string $message,
        array $details = [],
        int $status = 400,
        array $legacy = [],
    ): JsonResponse {
        $payload = array_merge($legacy, [
            'success' => false,
            'message' => $message,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ]);

        if ($details !== []) {
            $payload['error']['details'] = $details;
        }

        return response()->json($payload, $status);
    }

    public static function legacyError(
        string $legacyCode,
        string $message,
        int $status = 400,
        array $legacy = [],
        ?string $standardCode = null,
    ): JsonResponse {
        $payload = array_merge($legacy, [
            'success' => false,
            'message' => $message,
            'error' => $legacyCode,
        ]);

        if ($standardCode !== null) {
            $payload['error_code'] = $standardCode;
        }

        return response()->json($payload, $status);
    }
}
