<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

class DummyEngine implements \MyTester\ICodeCoverageEngine
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
            "file1.php" => [
                1 => 1,
                -1,
                1,
            ],
            "file2.php" => [
                1 => -1,
                1,
                -1,
            ],
            "file3.php" => [
                1 => 1,
                1,
            ],
        ];
    }
}
