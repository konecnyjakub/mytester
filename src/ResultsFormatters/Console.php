<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\IConsoleAwareResultsFormatter;
use MyTester\JobResult;
use MyTester\SkippedTest;
use MyTester\TestCase;
use MyTester\TestWarning;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;

/**
 * Console results formatter for Tester
 * Prints the results to console/standard output
 *
 * @author Jakub Konečný
 * @internal
 */
final class Console implements IConsoleAwareResultsFormatter
{
    public \Nette\CommandLine\Console $console;

    private string $folder;

    /** @var SkippedTest[] */
    private array $skipped = [];

    /** @var TestWarning[] */
    private array $warnings = [];

    private string $results = "";

    public function setup(): void
    {
        $files = Finder::findFiles("*.errors")->in($this->folder);
        foreach ($files as $name => $file) {
            try {
                FileSystem::delete($name);
            } catch (IOException) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
            }
        }
    }

    public function setTestsFolder(string $folder): void
    {
        $this->folder = $folder;
    }

    public function setConsole(\Nette\CommandLine\Console $console): void
    {
        $this->console = $console;
    }

    public function reportTestCase(TestCase $testCase): void
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
                        file_put_contents("$this->folder/$job->name.errors", $output);
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

    public function render(int $totalTime): string
    {
        ob_start();
        $results = $this->results;
        $rp = JobResult::PASSED->output();
        $rf = JobResult::FAILED->output();
        $rs = JobResult::SKIPPED->output();
        $rw = JobResult::WARNING->output();
        $results = str_replace($rf, $this->console->color("red", $rf), $results);
        $results = str_replace($rs, $this->console->color("yellow", $rs), $results);
        $results = str_replace($rw, $this->console->color("yellow", $rw), $results);
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
        $time = \Ayesh\PHP_Timer\Formatter::formatTime($totalTime);
        $resultsLine .= ", $time)";
        $resultsLine = $this->console->color((!$failed) ? "green" : "red", $resultsLine);
        echo $resultsLine . "\n";
        /** @var string $result */
        $result = ob_get_clean();
        return $result;
    }

    public function getOutputFileName(string $folder): string
    {
        return "php://output";
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
        $filenameSuffix = ".errors";
        $files = Finder::findFiles("*$filenameSuffix")->in($this->folder);
        /** @var \SplFileInfo $file */
        foreach ($files as $name => $file) {
            echo "--- " . $file->getBasename($filenameSuffix) . "\n";
            echo file_get_contents($name);
        }
    }
}
