<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Engines;

use MyTester\CodeCoverage\ICodeCoverageEngine;

final class TestEngine implements ICodeCoverageEngine
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
        $basePath = realpath(__DIR__ . "/../../../src");
        return [
            $basePath . "/ChainTestSuitesFinder.php" => [
                17 => 1,
                22 => 1,
                23 => 1,
                24 => 1,
                26 => 1,
            ],
            $basePath . "/Attributes/Skip.php" => [
                20 => 1,
            ],
            $basePath . "/Bridges/NetteRobotLoader/TestSuitesFinder.php" => [
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
        ];
    }
}
