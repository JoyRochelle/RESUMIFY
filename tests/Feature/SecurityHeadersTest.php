<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_application_adds_baseline_security_headers_to_responses()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_testing_csp_allows_local_development_assets_and_connections()
    {
        $response = $this->get('/');

        $response->assertHeader(
            'Content-Security-Policy',
            "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; connect-src * 'unsafe-inline' 'unsafe-eval' ws: wss: data: blob:"
        );
    }

    public function test_production_csp_uses_baseline_self_policy()
    {
        app()['env'] = 'production';

        $response = $this->get('/');

        $response->assertHeader(
            'Content-Security-Policy',
            "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self'"
        );
    }

    public function test_hsts_is_not_added_in_non_production_http_requests()
    {
        $response = $this->get('/');

        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_hsts_is_added_in_production_environment()
    {
        app()['env'] = 'production';

        $response = $this->get('/');

        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_hsts_is_added_for_secure_requests()
    {
        $response = $this->get('https://localhost/');

        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
}
