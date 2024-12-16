<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\ConsoleColors;
use MyTester\IConsoleAwareResultsFormatter;
use MyTester\JobResult;
use MyTester\SkippedTest;
use MyTester\TestCase;
use MyTester\TestWarning;

/**
 * Console results formatter for Tester
 * Prints the results to console/standard output
 *
 * @author Jakub Konečný
 */
final class Console extends AbstractResultsFormatter implements IConsoleAwareResultsFormatter
{
    private ConsoleColors $console;

    /** @var array<string, string> */
    private array $failures = [];

    /** @var SkippedTest[] */
    private array $skipped = [];

    /** @var TestWarning[] */
    private array $warnings = [];

    private string $results = "";

    public function setConsole(ConsoleColors $console): void
    {
        $this->console = $console;
    }

    public function reportTestCaseFinished(TestCase $testCase): void
    {
        $jobs = $testCase->jobs;
        foreach ($jobs as $job) {
            switch ($job->result) {
                case JobResult::SKIPPED:
                    $this->skipped[] = new SkippedTest($job->name, (is_string($job->skip) ? $job->skip : ""));
                    break;
                case JobResult::FAILED:
                    $output = $job->output;
                    if (strlen($output) > 0) {
                        $this->failures[$job->name] = $output;
                    }
                    break;
                case JobResult::WARNING:
                    $output = $job->output;
                    $output = str_replace("Warning: ", "", $output);
                    $this->warnings[] = new TestWarning($job->name, $output);
                    break;
            }
            $this->results .= $job->result->output();
        }
    }

    public function render(): string
    {
        ob_start();
        $results = $this->results;
        $rp = JobResult::PASSED->output();
        $rf = JobResult::FAILED->output();
        $rs = JobResult::SKIPPED->output();
        $rw = JobResult::WARNING->output();
        $results = str_replace($rf, $this->console->color($rf, "red"), $results);
        $results = str_replace($rs, $this->console->color($rs, "yellow"), $results);
        $results = str_replace($rw, $this->console->color($rw, "yellow"), $results);
        echo $results . "\n";
        $results = $this->results;
        $this->printWarnings();
        $this->printSkipped();
        $failed = str_contains($results, $rf);
        if (!$failed) {
            echo "\n";
            $resultsLine = "OK";
        } else {
            $this->printFailed();
            echo "\n";
            $resultsLine = "Failed";
        }
        $resultsLine .= " (" . strlen($results) . " tests";
        if (str_contains($results, $rp)) {
            $resultsLine .= ", " . substr_count($results, $rp) . " passed";
        }
        if (str_contains($results, $rw)) {
            $resultsLine .= ", " . substr_count($results, $rw) . " passed with warnings";
        }
        if ($failed) {
            $resultsLine .= ", " . substr_count($results, $rf) . " failed";
        }
        if (str_contains($results, $rs)) {
            $resultsLine .= ", " . substr_count($results, $rs) . " skipped";
        }
        $time = \Ayesh\PHP_Timer\Formatter::formatTime($this->totalTime);
        $resultsLine .= ", $time)";
        $resultsLine = $this->console->color($resultsLine, (!$failed) ? "green" : "red");
        echo $resultsLine . "\n";
        /** @var string $result */
        $result = ob_get_clean();
        return $result;
    }

    /**
     * Print info about tests with warnings
     */
    private function printWarnings(): void
    {
        foreach ($this->warnings as $testWarning) {
            echo $testWarning;
        }
    }

    /**
     * Print info about skipped tests
     */
    private function printSkipped(): void
    {
        foreach ($this->skipped as $skipped) {
            echo $skipped;
        }
    }

    /**
     * Print info about failed tests
     */
    private function printFailed(): void
    {
        foreach ($this->failures as $name => $text) {
            echo "--- " . $name . "\n$text\n";
        }
    }
}
