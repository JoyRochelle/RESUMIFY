<?php

namespace App\Domain\Ai\Data;

class ResumeBulletOptionsResponse extends AiProviderResponseValidator
{
    /**
     * @param array<int|string, mixed> $data
     * @return array<int, string>
     */
    public static function fromProvider(array $data): array
    {
        $options = self::assertStringList($data, 'bullet_options', 3, 500);

        if (count($options) !== 3) {
            self::invalid('bullet_options must contain exactly 3 items.');
        }

        return $options;
    }
}
