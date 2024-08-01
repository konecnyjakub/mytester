<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ICodeCoverageFormatter
{
    public function render(Report $report): string;
    public function getOutputFileName(string $folder): string;
}
