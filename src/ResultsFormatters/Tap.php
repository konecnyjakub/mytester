<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\IResultsFormatter;
use MyTester\JobResult;
use MyTester\TestCase;

/**
 * TAP results formatter for Tester
 * Outputs the results to console/standard output in TAP format
 * @see http://testanything.org/tap-version-14-specification.html
 *
 * @author Jakub Konečný
 * @internal
 */
final class Tap implements IResultsFormatter
{
    public const TAP_VERSION = 14;

    /** @var TestCase[] */
    private array $testCases = [];

    public function setup(): void
    {
    }

    public function setTestsFolder(string $folder): void
    {
    }

    public function reportTestCase(TestCase $testCase): void
    {
        $this->testCases[] = $testCase;
    }

    public function render(int $totalTime): string
    {
        ob_start();

        echo "TAP version " . static::TAP_VERSION . "\n";

        $totalTests = 0;

        foreach ($this->testCases as $testCase) {
            foreach ($testCase->jobs as $job) {
                $totalTests++;
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
                printf("%s %d%s\n", $result, $totalTests, $description);
            }
        }

        echo "1..$totalTests\n";

        /** @var string $result */
        $result = ob_get_clean();
        return $result;
    }

    public function getOutputFileName(string $folder): string
    {
        return "php://output";
    }
}
