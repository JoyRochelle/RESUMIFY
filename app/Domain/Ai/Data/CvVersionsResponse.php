<?php

namespace App\Domain\Ai\Data;

class CvVersionsResponse extends AiProviderResponseValidator
{
    private const ALLOWED_SECTION_TYPES = [
        'personal_info',
        'work_experience',
        'education',
        'skills',
        'target_job',
        'certifications',
        'projects',
        'languages',
    ];

    /**
     * @param array<int|string, mixed> $data
     * @return array<int, array<string, mixed>>
     */
    public static function fromProvider(array $data): array
    {
        $sections = self::assertList($data, 'cv_sections', 30);

        return array_map(function (mixed $section, int $index): array {
            $section = self::assertObject($section, "cv_sections.{$index}");
            self::assertExactKeys($section, ['type', 'title', 'content'], "cv_sections.{$index}");

            return [
                'type' => self::assertEnum($section['type'], self::ALLOWED_SECTION_TYPES, "cv_sections.{$index}.type"),
                'title' => self::assertString($section['title'], "cv_sections.{$index}.title", 100),
                'content' => self::content($section['content'], "cv_sections.{$index}.content"),
            ];
        }, $sections, array_keys($sections));
    }

    /**
     * @return array<int|string, mixed>
     */
    private static function content(mixed $value, string $path): array
    {
        if (!is_array($value) || count($value) > 40) {
            self::invalid("{$path} must be an object or list with at most 40 entries.");
        }

        return self::walkContent($value, $path, 0);
    }

    /**
     * @param array<int|string, mixed> $value
     * @return array<int|string, mixed>
     */
    private static function walkContent(array $value, string $path, int $depth): array
    {
        if ($depth > 4 || count($value) > 40) {
            self::invalid("{$path} is too deeply nested or too large.");
        }

        $validated = [];

        foreach ($value as $key => $item) {
            if ((!is_string($key) && !is_int($key)) || (is_string($key) && mb_strlen($key) > 80)) {
                self::invalid("{$path} contains an invalid key.");
            }

            $itemPath = "{$path}.{$key}";

            if (is_array($item)) {
                $validated[$key] = self::walkContent($item, $itemPath, $depth + 1);
                continue;
            }

            if ($item === null || is_bool($item) || is_int($item) || is_float($item)) {
                $validated[$key] = $item;
                continue;
            }

            $validated[$key] = self::assertString($item, $itemPath, 2000);
        }

        return $validated;
    }
}
