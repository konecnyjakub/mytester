<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\ConsoleColors;
use MyTester\ConsoleAwareResultsFormatter;
use MyTester\JobResult;

/**
 * TestDox results formatter for Tester
 * Outputs the results to console/standard output in TestDox format (can be set to output into a file)
 *
 * @author Jakub Konečný
 */
final class TestDox extends AbstractResultsFormatter implements ConsoleAwareResultsFormatter
{
    private ConsoleColors $console;

    public function setConsole(ConsoleColors $console): void
    {
        $this->console = $console;
    }

    public function render(): string
    {
        ob_start();

        foreach ($this->testSuites as $index => $testSuite) {
            if ($index > 0) {
                echo "\n";
            }
            echo $testSuite->getSuiteName() . "\n";
            foreach ($testSuite->jobs as $job) {
                printf(
                    " %s %s\n",
                    $this->getOutput($job->result),
                    $job->nameWithDataSet
                );
            }
        }

        /** @var string $result */
        $result = ob_get_clean();
        return $result;
    }

    private function getOutput(JobResult $jobResult): string
    {
        return match ($jobResult) {
            JobResult::PASSED => $this->isOutputConsole() ? $this->console->color("✔", "green") : "[ ]",
            JobResult::SKIPPED => $this->isOutputConsole() ? $this->console->color("↩", "blue") : "[x]",
            JobResult::WARNING => $this->isOutputConsole() ? $this->console->color("⚠", "yellow") : "[x]",
            JobResult::FAILED => $this->isOutputConsole() ? $this->console->color("✘", "red") : "[x]",
        };
    }

    public function setOutputFileName(string $baseFileName): void
    {
        $this->baseFileName = $baseFileName;
    }
}
