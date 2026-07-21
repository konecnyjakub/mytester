<?php
declare(strict_types=1);

namespace MyTester\Config;

use Internal\Toml\Toml;

/**
 * @author Jakub Konečný
 * @internal
 */
final class TomlFileConfigAdapter extends BaseFileConfigAdapter
{
    public function getPriority(): int
    {
        return PHP_INT_MAX - 2;
    }

    public function isAvailable(): bool
    {
        return class_exists(Toml::class);
    }

    protected function parseConfig(): array
    {
        foreach ($this->getFileNames("toml") as $fileName) {
            if (is_file($fileName)) {
                $content = @file_get_contents($fileName); // phpcs:ignore Generic.PHP.NoSilencedErrors
                if ($content === false) {
                    continue;
                }
                return Toml::parseToArray($content); // @phpstan-ignore return.type
            }
        }
        return [];
    }
}
