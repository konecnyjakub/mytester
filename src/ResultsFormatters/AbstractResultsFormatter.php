<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use Ayesh\PHP_Timer\Timer;
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
    private const string TIMER_NAME = "My Tester";

    /** @var TestCase[] All test cases that have finished (no matter their result) */
    protected array $testCases = [];

    protected string $baseFileName = "php://output";

    /** @var int Total elapsed time in milliseconds */
    protected int $totalTime = 0;

    public function reportTestsStarted(array $testCases): void
    {
        Timer::start(self::TIMER_NAME);
    }

    public function reportTestsFinished(array $testCases): void
    {
        Timer::stop(self::TIMER_NAME);
        // @phpstan-ignore argument.type
        $totalTime = (int) Timer::read(self::TIMER_NAME, Timer::FORMAT_PRECISE);
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
        if ($this->isOutputConsole()) {
            return $this->baseFileName;
        }
        return "$folder/{$this->baseFileName}";
    }

    public function setOutputFileName(string $baseFileName): void
    {
    }

    protected function isOutputConsole(): bool
    {
        return !Helper::isFileOutput($this->baseFileName);
    }
}
