<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ICodeCoverageFormatter
{
    public function render(array $data): string;
}
