<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Report for code coverage - one file
 * Contains all relevant data in a convenient form
 *
 * @author Jakub Konečný
 */
final readonly class ReportFile
{
    public int $linesTotal;
    public int $linesCovered;
    public int $coveragePercent;

    /**
     * @param \ReflectionClass<object>[] $classes
     * @param \ReflectionFunction[] $functions
     * @param array<int, int> $data
     */
    public function __construct(public string $name, public array $classes, public array $functions, public array $data)
    {
        $totalLines = 0;
        $coveredLines = 0;
        foreach ($data as $line) {
            $totalLines++;
            if ($line > 0) {
                $coveredLines++;
            }
        }
        $coveragePercent = ($totalLines === 0) ? 0 : (int) (($coveredLines / $totalLines) * 100);

        $this->coveragePercent = $coveragePercent;
        $this->linesTotal = $totalLines;
        $this->linesCovered = $coveredLines;
    }
}
