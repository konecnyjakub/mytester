<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Code coverage formatter that allows customization of file name of the report
 *
 * @author Jakub Konečný
 * @internal
 */
interface ICodeCoverageCustomFileNameFormatter extends ICodeCoverageFormatter
{
    /**
     * Set file name for output that should be used instead of the default one
     */
    public function setOutputFileName(string $baseFileName): void;
}
