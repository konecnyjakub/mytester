<?php
declare(strict_types=1);

namespace MyTester\Config;

use JsonException;

/**
 * @author Jakub Konečný
 * @internal
 */
final class JsonlFileConfigAdapter extends BaseFileConfigAdapter
{
    public function getPriority(): int
    {
        return PHP_INT_MAX - 4;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * @throws JsonException
     */
    protected function parseConfig(): array
    {
        foreach ($this->getFileNames("json") as $fileName) {
            if (is_file($fileName)) {
                $content = @file_get_contents($fileName); // phpcs:ignore Generic.PHP.NoSilencedErrors
                if ($content === false) {
                    continue;
                }
                // @phpstan-ignore return.type
                return json_decode($content, flags: JSON_THROW_ON_ERROR | JSON_OBJECT_AS_ARRAY);
            }
        }
        return [];
    }
}
