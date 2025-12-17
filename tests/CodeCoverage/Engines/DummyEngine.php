<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Engines;

use MyTester\CodeCoverage\CodeCoverageEngine;

class DummyEngine implements CodeCoverageEngine
{
    public function getName(): string
    {
        return "dummy";
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
        return [
            "/var/project/src/file1.php" => [
                1 => 1,
                -1,
                1,
            ],
            "/var/project/src/sub1/file2.php" => [
                1 => -1,
                1,
                -1,
            ],
            "/var/project/src/sub2/file3.php" => [
                1 => 1,
                1,
            ],
        ];
    }
}
