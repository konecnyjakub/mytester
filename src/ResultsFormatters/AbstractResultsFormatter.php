<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\IResultsFormatter;
use MyTester\TestCase;

/**
 * Base results formatter for Tester
 * Allows defining only necessary method for the results formatter
 *
 * @author Jakub Konečný
 */
abstract class AbstractResultsFormatter implements IResultsFormatter
{
    /** @var TestCase[] All test cases that have finished (no matter their result) */
    protected array $testCases = [];

    protected string $baseFileName = "php://output";

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
        if ($this->baseFileName === "php://output") {
            return $this->baseFileName;
        }
        return "$folder/{$this->baseFileName}";
    }
}
