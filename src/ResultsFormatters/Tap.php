<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\IResultsFormatter;
use MyTester\JobResult;
use MyTester\TestCase;

/**
 * TAP results formatter for Tester
 * Outputs the results to console/standard output in TAP format (can be set to output into a file)
 * @see http://testanything.org/tap-version-14-specification.html
 *
 * @author Jakub Konečný
 */
final class Tap extends AbstractResultsFormatter implements IResultsFormatter
{
    public const int TAP_VERSION = 14;

    private int $totalTests = 0;

    /**
     * @param TestCase[] $testCases
     */
    public function reportTestsStarted(array $testCases): void
    {
        parent::reportTestsStarted($testCases);
        foreach ($testCases as $testCase) {
            $this->totalTests += count($testCase->jobs);
        }
    }

    public function render(): string
    {
        ob_start();

        echo "TAP version " . self::TAP_VERSION . "\n";
        echo "1..{$this->totalTests}\n";

        $currentTest = 0;

        foreach ($this->testCases as $testCase) {
            foreach ($testCase->jobs as $job) {
                $currentTest++;
                $result = match ($job->result) {
                    JobResult::FAILED => "not ok",
                    default => "ok",
                };
                $output = $job->output;
                $output = (string) str_replace("\n", " ", $output);
                $output = (string) str_replace("Warning: ", "", $output);
                $output = trim($output);
                $description = match ($job->result) {
                    JobResult::FAILED => " - $output",
                    JobResult::SKIPPED => (is_string($job->skip) ? " - {$job->skip}" : "") . " # SKIP",
                    JobResult::WARNING => " - $output # TODO",
                    default => "",
                };
                printf("%s %d%s\n", $result, $currentTest, $description);
            }
        }

        /** @var string $result */
        $result = ob_get_clean();
        return $result;
    }

    public function setOutputFileName(string $baseFileName): void
    {
        $this->baseFileName = $baseFileName;
    }
}
