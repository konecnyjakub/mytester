<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Engines;

use MyTester\CodeCoverage\CodeCoverageEngine;

final class TestEngine implements CodeCoverageEngine
{
    public function getName(): string
    {
        return "test";
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function start(): void
    {
    }

    public function collect(): array
    {
        $ds = DIRECTORY_SEPARATOR;
        $basePath = realpath(__DIR__ . "/../../../src") . $ds;
        return [
            $basePath . "ChainTestSuitesFinder.php" => [
                17 => 1,
                22 => 1,
                23 => 1,
                24 => 1,
                26 => 1,
            ],
            $basePath . "Attributes{$ds}Skip.php" => [
                20 => 1,
            ],
            $basePath . "Bridges{$ds}NetteRobotLoader{$ds}TestSuitesFinder.php" => [
                18 => 1,
                19 => -1,
                21 => 1,
                22 => 1,
                23 => 1,
                24 => 1,
                25 => 1,
                26 => 1,
                27 => 1,
                28 => 1,
                29 => 1,
                30 => 1,
                31 => 1,
                32 => 1,
                33 => 1,
                36 => 1,
                41 => 1,
            ],
            $basePath . "functions.php" => [
                9 => 1,
                10 => 1,
                11 => 1,
                12 => 0,
                14 => 1,
                15 => 1,
                16 => 1,
                18 => 1,
                20 => 1,
            ],
            $basePath . "Bridges{$ds}NetteDI{$ds}TCompiledContainer.php" => [
                16 => 1,
                26 => 1,
                31 => 1,
            ],
        ];
    }
}
