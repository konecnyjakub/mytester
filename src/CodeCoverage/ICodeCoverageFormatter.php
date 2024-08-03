<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ICodeCoverageFormatter
{
    /**
     * Generates and returns a code coverage report as a string
     */
    public function render(Report $report): string;

    /**
     * Returns file name to which result of {@see self::render()} should written. The file does not have to exist yet
     * It can be an absolute path or standard output (or anything accepted by {@see fopen()})
     */
    public function getOutputFileName(string $folder): string;
}
