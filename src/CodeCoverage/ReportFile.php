<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Report for code coverage - one file
 * Contains all relevant data in a convenient form
 *
 * @author Jakub Konečný
 * @internal
 */
final readonly class ReportFile
{
    use \Nette\SmartObject;

    public string $name;
    public array $data;
    public int $linesTotal;
    public int $linesCovered;
    public int $coveragePercent;

    public function __construct(string $name, array $data)
    {
        $totalLines = 0;
        $coveredLines = 0;
        foreach ($data as $line) {
            $totalLines++;
            if ($line > 0) {
                $coveredLines++;
            }
        }
        $coveragePercent = (int) (($coveredLines / $totalLines) * 100);

        $this->name = $name;
        $this->data = $data;
        $this->coveragePercent = $coveragePercent;
        $this->linesTotal = $totalLines;
        $this->linesCovered = $coveredLines;
    }
}
