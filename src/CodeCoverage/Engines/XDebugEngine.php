<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Engines;

use MyTester\CodeCoverage\ICodeCoverageEngine;

/**
 * XDebug engine for code coverage collector
 * Requires PHP extension XDebug
 *
 * @author Jakub Konečný
 * @internal
 */
final class XDebugEngine implements ICodeCoverageEngine
{
    public function getName(): string
    {
        return "XDebug";
    }

    public function isAvailable(): bool
    {
        return extension_loaded('xdebug');
    }

    public function start(): void
    {
        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    }

    public function collect(): array
    {
        $positive = $negative = [];

        foreach (xdebug_get_code_coverage() as $file => $lines) {
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
