<?php
declare(strict_types=1);

namespace MyTester\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Jakub Konečný
 * @internal
 */
final class YamlFileConfigAdapter extends BaseFileConfigAdapter
{
    public function getPriority(): int
    {
        return PHP_INT_MAX - 3;
    }

    public function isAvailable(): bool
    {
        return class_exists(Yaml::class);
    }

    /**
     * @throws ParseException
     */
    protected function parseConfig(): array
    {
        foreach ($this->getFileNames("neon") as $fileName) {
            if (is_file($fileName)) {
                /** @var array<string, mixed> $config */
                $config = Yaml::parseFile($fileName);
                return $config;
            }
        }
        return [];
    }
}
