<?php

namespace App\Domain\Ai\Data;

use App\Exceptions\InvalidAiProviderResponseException;

abstract class AiProviderResponseValidator
{
    protected const MAX_STRING_LENGTH = 2000;

    /**
     * @param array<int|string, mixed> $data
     * @param array<int, string> $keys
     */
    protected static function assertExactKeys(array $data, array $keys, string $path): void
    {
        $actual = array_keys($data);
        $missing = array_diff($keys, $actual);

        if ($missing !== []) {
            self::invalid("{$path} is missing required fields: " . implode(', ', $missing) . '.');
        }
    }

    protected static function assertIntRange(mixed $value, int $min, int $max, string $path): int
    {
        if (!is_int($value) || $value < $min || $value > $max) {
            self::invalid("{$path} must be an integer between {$min} and {$max}.");
        }

        return $value;
    }

    protected static function assertBool(mixed $value, string $path): bool
    {
        if (!is_bool($value)) {
            self::invalid("{$path} must be a boolean.");
        }

        return $value;
    }

    protected static function assertString(mixed $value, string $path, int $max = self::MAX_STRING_LENGTH): string
    {
        if (!is_string($value) || trim($value) === '' || mb_strlen($value) > $max) {
            self::invalid("{$path} must be a non-empty string up to {$max} characters.");
        }

        return trim($value);
    }

    /**
     * @param array<int, string> $allowed
     */
    protected static function assertEnum(mixed $value, array $allowed, string $path): string
    {
        $value = self::assertString($value, $path, 100);

        if (!in_array($value, $allowed, true)) {
            self::invalid("{$path} has an invalid value.");
        }

        return $value;
    }

    /**
     * @return array<int, string>
     */
    protected static function assertStringList(mixed $value, string $path, int $maxItems, int $maxStringLength = 255): array
    {
        if (!is_array($value) || !array_is_list($value) || count($value) > $maxItems) {
            self::invalid("{$path} must be a list with at most {$maxItems} items.");
        }

        return array_map(
            fn (mixed $item, int $index): string => self::assertString($item, "{$path}.{$index}", $maxStringLength),
            $value,
            array_keys($value),
        );
    }

    protected static function assertList(mixed $value, string $path, int $maxItems): array
    {
        if (!is_array($value) || !array_is_list($value) || count($value) > $maxItems) {
            self::invalid("{$path} must be a list with at most {$maxItems} items.");
        }

        return $value;
    }

    protected static function assertObject(mixed $value, string $path): array
    {
        if (!is_array($value) || array_is_list($value)) {
            self::invalid("{$path} must be an object.");
        }

        return $value;
    }

    protected static function invalid(string $message): never
    {
        throw new InvalidAiProviderResponseException($message);
    }
}
