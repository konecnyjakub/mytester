<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Formatters;

use MyTester\CodeCoverage\ICodeCoverageCustomFileNameFormatter;
use MyTester\CodeCoverage\Report;

/**
 * Text formatter for code coverage
 * Report total coverage and coverage per file (both percentage and number of lines)
 *
 * @author Jakub Konečný
 */
final class TextFormatter implements ICodeCoverageCustomFileNameFormatter
{
    private string $baseFileName = "coverage.txt";

    public function render(Report $report): string
    {
        $result = sprintf("Code coverage report for %s\n\n", $report->sourcePath);
        $result .= sprintf(
            "Total code coverage: %d%% (%d out of %d lines)\n\n",
            $report->coveragePercent,
            $report->linesCovered,
            $report->linesTotal
        );
        $result .= "Code coverage per file:\n";

        $files = $report->files;
        sort($files);
        foreach ($files as $reportFile) {
            $result .= sprintf(
                "%s: %d%% (%d out of %d lines)\n",
                $reportFile->name,
                $reportFile->coveragePercent,
                $reportFile->linesCovered,
                $reportFile->linesTotal
            );
        }

        return $result;
    }

    public function getOutputFileName(string $folder): string
    {
        return "$folder/{$this->baseFileName}";
    }

    public function setOutputFileName(string $baseFileName): void
    {
        $this->baseFileName = $baseFileName;
    }
}
