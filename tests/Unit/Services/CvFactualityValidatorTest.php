<?php

namespace Tests\Unit\Services;

use App\Services\CvFactualityValidator;
use Tests\TestCase;

class CvFactualityValidatorTest extends TestCase
{
    private function validator(): CvFactualityValidator
    {
        return new CvFactualityValidator();
    }

    private function sourceSections(): array
    {
        return [
            [
                'type' => 'work_experience',
                'title' => 'Experience',
                'content' => [
                    [
                        'company' => 'Acme Corp',
                        'role' => 'Software Engineer',
                        'bullets' => [
                            'Led a small team and increased deployment speed by 20%.',
                            'Maintained internal tools using PHP and Laravel.',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'skills',
                'title' => 'Skills',
                'content' => ['PHP', 'Laravel', 'MySQL'],
            ],
        ];
    }

    public function test_clean_rewrite_of_existing_facts_produces_no_warning(): void
    {
        $adapted = [
            [
                'type' => 'work_experience',
                'title' => 'Experience',
                'content' => [
                    [
                        'company' => 'Acme Corp',
                        'role' => 'Software Engineer',
                        'bullets' => [
                            'Spearheaded a compact team, lifting deployment speed by 20%.',
                            'Kept internal tools running smoothly with PHP and Laravel.',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'skills',
                'title' => 'Skills',
                'content' => ['PHP', 'Laravel', 'MySQL'],
            ],
        ];

        $result = $this->validator()->validate($this->sourceSections(), $adapted);

        $this->assertFalse($result['flagged']);
        $this->assertSame([], $result['numbers']);
        $this->assertSame([], $result['entities']);
    }

    public function test_detects_fabricated_number_not_present_in_source(): void
    {
        $adapted = [
            [
                'type' => 'work_experience',
                'title' => 'Experience',
                'content' => [
                    [
                        'company' => 'Acme Corp',
                        'role' => 'Software Engineer',
                        'bullets' => [
                            'Led a small team and increased deployment speed by 20%, boosting revenue by 45%.',
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->validator()->validate($this->sourceSections(), $adapted);

        $this->assertTrue($result['flagged']);
        $this->assertContains('45', $result['numbers']);
        $this->assertNotContains('20', $result['numbers']);
    }

    public function test_detects_fabricated_company_name_not_present_in_source(): void
    {
        $adapted = [
            [
                'type' => 'work_experience',
                'title' => 'Experience',
                'content' => [
                    [
                        'company' => 'Acme Corp',
                        'role' => 'Software Engineer',
                        'bullets' => [
                            'Partnered closely with engineers at Google to ship internal tools.',
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->validator()->validate($this->sourceSections(), $adapted);

        $this->assertTrue($result['flagged']);
        $this->assertContains('Google', $result['entities']);
    }

    public function test_detects_fabricated_certification_not_present_in_source(): void
    {
        $adapted = [
            [
                'type' => 'skills',
                'title' => 'Skills',
                'content' => ['PHP', 'Laravel', 'MySQL', 'AWS Certified Solutions Architect'],
            ],
        ];

        $result = $this->validator()->validate($this->sourceSections(), $adapted);

        $this->assertTrue($result['flagged']);
        $this->assertContains('AWS Certified Solutions Architect', $result['entities']);
    }

    public function test_does_not_flag_facts_already_present_in_source(): void
    {
        $adapted = [
            [
                'type' => 'work_experience',
                'title' => 'Experience',
                'content' => [
                    [
                        'company' => 'Acme Corp',
                        'role' => 'Software Engineer',
                        'bullets' => [
                            'Grew deployment speed at Acme Corp by 20% using PHP and Laravel.',
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->validator()->validate($this->sourceSections(), $adapted);

        $this->assertFalse($result['flagged']);
    }
}
