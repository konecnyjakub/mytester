<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Percent formatter for code coverage
 * Reports only total % of code coverage
 *
 * @author Jakub Konečný
 * @internal
 */
final class PercentFormatter implements ICodeCoverageFormatter
{
    public function render(array $data): string
    {
        $result = "Calculating code coverage... ";
        $totalLines = 0;
        $coveredLines = 0;
        foreach ($data as $file) {
            foreach ($file as $line) {
                $totalLines++;
                if ($line > 0) {
                    $coveredLines++;
                }
            }
        }
        $coveragePercent = (int) (($coveredLines / $totalLines) * 100);
        $result .= $coveragePercent . "% covered\n";
        return $result;
    }
}
