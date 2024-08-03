<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Formatters;

use MyTester\CodeCoverage\ICodeCoverageFormatter;
use MyTester\CodeCoverage\Report;

/**
 * Percent formatter for code coverage
 * Reports only total % of code coverage
 *
 * @author Jakub Konečný
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
