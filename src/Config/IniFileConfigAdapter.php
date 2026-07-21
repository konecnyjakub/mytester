<?php
declare(strict_types=1);

namespace MyTester\Config;

/**
 * @author Jakub Konečný
 * @internal
 */
final class IniFileConfigAdapter extends BaseFileConfigAdapter
{
    public function getPriority(): int
    {
        return PHP_INT_MAX - 5;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    protected function parseConfig(): array
    {
        foreach ($this->getFileNames("ini") as $fileName) {
            if (is_file($fileName)) {
                /** @var array<string, mixed> $config */
                $config = parse_ini_file($fileName, true, INI_SCANNER_TYPED);
                return $config;
            }
        }
        return [];
    }
}
