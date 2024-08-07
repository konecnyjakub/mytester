<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\IResultsFormatter;
use MyTester\TestCase;

/**
 * Base results formatter for Tester
 *
 * @author Jakub Konečný
 * @internal
 */
abstract class AbstractResultsFormatter implements IResultsFormatter
{
    /** @var TestCase[] */
    protected array $testCases = [];

    /** @var int Total elapsed time in milliseconds */
    protected int $totalTime = 0;

    public function setup(): void
    {
    }

    public function reportTestsStarted(array $testCases): void
    {
    }

    public function reportTestsFinished(array $testCases, int $totalTime): void
    {
        $this->totalTime = $totalTime;
    }

    public function reportTestCaseStarted(TestCase $testCase): void
    {
    }

    public function reportTestCaseFinished(TestCase $testCase): void
    {
        $this->testCases[] = $testCase;
    }

    public function getOutputFileName(string $folder): string
    {
        return "php://output";
    }
}
