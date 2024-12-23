<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Engines;

use MyTester\CodeCoverage\ICodeCoverageEngine;

/**
 * Pcov engine for code coverage collector
 * Requires PHP extension pcov
 *
 * @author Jakub Konečný
 */
final class PcovEngine implements ICodeCoverageEngine
{
    public function getName(): string
    {
        return "pcov";
    }

    public function isAvailable(): bool
    {
        return extension_loaded("pcov");
    }

    public function start(): void
    {
        \pcov\start();
    }

    public function collect(): array
    {
        $positive = $negative = [];

        \pcov\stop();

        /**
         * @var string $file
         * @var array<int, int> $lines
         */
        foreach (\pcov\collect() as $file => $lines) {
            if (!file_exists($file)) {
                continue;
            }

            foreach ($lines as $number => $value) {
                if ($value > 0) {
                    $positive[$file][$number] = $value;
                } else {
                    $negative[$file][$number] = $value;
                }
            }
        }

        return array_replace_recursive($negative, $positive); // @phpstan-ignore return.type
    }
}
