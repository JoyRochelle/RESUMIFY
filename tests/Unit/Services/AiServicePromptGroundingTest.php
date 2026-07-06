<?php

namespace Tests\Unit\Services;

use App\Services\AiService;
use ReflectionMethod;
use Tests\TestCase;

class AiServicePromptGroundingTest extends TestCase
{
    private function invokePrivate(string $method, array $args): string
    {
        $reflection = new ReflectionMethod(AiService::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke(new AiService(), ...$args);
    }

    public function test_cv_version_prompt_always_includes_anti_fabrication_grounding_rules(): void
    {
        $prompt = $this->invokePrivate('buildCvVersionPrompt', [
            'Focus on team leadership.',
            'Senior Backend Engineer job description.',
            [['type' => 'skills', 'title' => 'Skills', 'content' => ['PHP']]],
        ]);

        $this->assertStringContainsString('STRICT GROUNDING RULES', $prompt);
        $this->assertStringContainsString('NEVER invent or add a company', $prompt);
        $this->assertStringContainsString('NEVER invent or add a number', $prompt);
    }

    public function test_refine_bullet_prompt_always_includes_anti_fabrication_grounding_rules(): void
    {
        $prompt = $this->invokePrivate('buildRefineBulletPrompt', [
            'Worked on backend services.',
            'Software Engineer',
        ]);

        $this->assertStringContainsString('STRICT GROUNDING RULES', $prompt);
        $this->assertStringContainsString('NEVER invent or add a company', $prompt);
        $this->assertStringContainsString('NEVER invent or add a number', $prompt);
    }

    public function test_refine_bullet_prompt_no_longer_invites_fabricated_quantification(): void
    {
        $prompt = $this->invokePrivate('buildRefineBulletPrompt', [
            'Worked on backend services.',
            null,
        ]);

        $this->assertStringNotContainsString('quantifying results where plausible', $prompt);
    }
}
