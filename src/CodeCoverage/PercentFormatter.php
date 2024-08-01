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
    public function render(Report $report): string
    {
        $result = "Calculating code coverage... ";
        $result .= $report->coveragePercent . "% covered\n";
        return $result;
    }

    public function getOutputFileName(string $folder): string
    {
        return "php://output";
    }
}
