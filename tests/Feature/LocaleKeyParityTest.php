<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * A key added to one locale and forgotten in the other renders the raw dotted
 * key on screen ("messages.editor.date_picker.year"), which no page test would
 * catch unless it happened to assert that exact string.
 */
class LocaleKeyParityTest extends TestCase
{
    /** @return list<string> */
    private function flatten(array $translations, string $prefix = ''): array
    {
        $keys = [];

        foreach ($translations as $key => $value) {
            $path = $prefix === '' ? (string) $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $keys = array_merge($keys, $this->flatten($value, $path));
                continue;
            }

            $keys[] = $path;
        }

        return $keys;
    }

    public function test_english_and_indonesian_message_keys_match(): void
    {
        $en = $this->flatten(require lang_path('en/messages.php'));
        $id = $this->flatten(require lang_path('id/messages.php'));

        $this->assertSame([], array_values(array_diff($en, $id)), 'Keys present in en but missing from id.');
        $this->assertSame([], array_values(array_diff($id, $en)), 'Keys present in id but missing from en.');
    }
}
