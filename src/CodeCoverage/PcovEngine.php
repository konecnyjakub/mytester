<?php

declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Pcov engine for code coverage collector
 *
 * @author Jakub Konečný
 * @internal
 */
final class PcovEngine implements \MyTester\ICodeCoverageEngine
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

        return array_replace_recursive($negative, $positive);
    }
}
