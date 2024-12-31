<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use Ayesh\PHP_Timer\Timer;
use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use MyTester\Events\TestSuiteFinished;
use MyTester\Events\TestSuiteStarted;
use MyTester\Events\TestsFinished;
use MyTester\Events\TestsStarted;
use MyTester\IResultsFormatter;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
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

    /** @var TestCase[] All test suites that have finished (no matter their result) */
    protected array $testSuites = [];

    protected string $baseFileName = "php://output";

    /** @var int Total elapsed time in milliseconds */
    protected int $totalTime = 0;

    public static function getSubscribedEvents(): iterable
    {
        return [
            TestsStarted::class => [
                ["reportTestsStarted", AutoListenerProvider::PRIORITY_HIGH, ],
            ],
            TestsFinished::class => [
                ["reportTestsFinished", AutoListenerProvider::PRIORITY_HIGH, ],
            ],
            TestSuiteStarted::class => [
                ["reportTestCaseStarted", AutoListenerProvider::PRIORITY_HIGH, ],
            ],
            TestSuiteFinished::class => [
                ["reportTestCaseFinished", AutoListenerProvider::PRIORITY_HIGH, ],
            ],
        ];
    }

    public function reportTestsStarted(TestsStarted $event): void
    {
        Timer::start(self::TIMER_NAME);
    }

    public function reportTestsFinished(TestsFinished $event): void
    {
        Timer::stop(self::TIMER_NAME);
        // @phpstan-ignore argument.type, cast.int
        $totalTime = (int) Timer::read(self::TIMER_NAME, Timer::FORMAT_PRECISE);
        $this->totalTime = $totalTime;
    }

    public function reportTestCaseStarted(TestSuiteStarted $event): void
    {
    }

    public function reportTestCaseFinished(TestSuiteFinished $event): void
    {
        $this->testSuites[] = $event->testSuite;
    }

    public function outputResults(string $outputFolder): void
    {
        $filename = $this->getOutputFileName($outputFolder);
        if (ResultsHelper::isFileOutput($filename)) {
            echo "Results are redirected into file $filename\n";
        }

        /** @var resource $outputFile */
        $outputFile = fopen($filename, "w");
        fwrite($outputFile, $this->render());
        fclose($outputFile);
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

    /**
     * Generates and returns results of Tester run as string
     */
    abstract public function render(): string;

    protected function isOutputConsole(): bool
    {
        return !Helper::isFileOutput($this->baseFileName);
    }
}
