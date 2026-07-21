<?php
declare(strict_types=1);

namespace MyTester\Config;

use Nette\Neon\Neon;

/**
 * @author Jakub Konečný
 * @internal
 */
final class NeonFileConfigAdapter extends BaseFileConfigAdapter
{
    public function getPriority(): int
    {
        return PHP_INT_MAX - 1;
    }

    public function isAvailable(): bool
    {
        return class_exists(Neon::class);
    }

    /**
     * @throws \Nette\Neon\Exception
     */
    protected function parseConfig(): array
    {
        foreach ($this->getFileNames("neon") as $fileName) {
            if (is_file($fileName)) {
                /** @var array<string, mixed> $config */
                $config = Neon::decodeFile($fileName);
                return $config;
            }
        }
        return [];
    }
}
