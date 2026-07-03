<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_application_adds_security_headers_to_responses()
    {
        // Act: Make a request to a public route (e.g., the home page)
        $response = $this->get('/');

        // Assert: Ensure the response is successful and has the expected security headers
        $response->assertStatus(200);

        // Security headers expected by Task 7
        $response->assertHeader('X-Frame-Options');
        $response->assertHeader('X-Content-Type-Options');
        $response->assertHeader('Referrer-Policy');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_hsts_is_not_added_in_non_production_http_request()
    {
        $response = $this->get('/');
        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_hsts_is_added_in_production_environment()
    {
        app()['env'] = 'production';

        $response = $this->get('/');
        $response->assertHeader('Strict-Transport-Security');
    }

    public function test_hsts_is_added_for_secure_requests()
    {
        // Simulate a secure request (HTTPS)
        $response = $this->get('https://localhost/');
        $response->assertHeader('Strict-Transport-Security');
    }
}
