<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ICodeCoverageCustomFileNameFormatter extends ICodeCoverageFormatter
{
    public function setOutputFileName(string $baseFileName): void;
}
