<?php

namespace Tests\Feature;

use App\Support\ApiResponse;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response_wraps_data_and_meta_while_preserving_legacy_success_flag(): void
    {
        $response = TestResponse::fromBaseResponse(ApiResponse::success(
            data: ['score' => 82],
            meta: ['requestId' => 'ats-123']
        ));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'score' => 82,
                ],
                'meta' => [
                    'requestId' => 'ats-123',
                ],
            ]);
    }

    public function test_error_response_uses_standard_error_shape_while_preserving_legacy_fields(): void
    {
        $response = TestResponse::fromBaseResponse(ApiResponse::error(
            code: 'AI_PROVIDER_INVALID_RESPONSE',
            message: 'The AI provider returned an invalid response.',
            status: 502
        ));

        $response->assertStatus(502)
            ->assertJson([
                'success' => false,
                'message' => 'The AI provider returned an invalid response.',
                'error' => [
                    'code' => 'AI_PROVIDER_INVALID_RESPONSE',
                    'message' => 'The AI provider returned an invalid response.',
                ],
            ]);
    }
}
