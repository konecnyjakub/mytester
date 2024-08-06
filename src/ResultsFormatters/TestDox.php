<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\IResultsFormatter;
use MyTester\JobResult;
use MyTester\TestCase;

/**
 * TestDox results formatter for Tester
 * Outputs the results to console/standard output in TestDox format
 *
 * @author Jakub Konečný
 * @internal
 */
final class TestDox implements IResultsFormatter
{
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

        foreach ($this->testCases as $index => $testCase) {
            if ($index > 0) {
                echo "\n";
            }
            echo $testCase->getSuiteName() . "\n";
            foreach ($testCase->jobs as $job) {
                printf(
                    " [%s] %s\n",
                    ($job->result === JobResult::PASSED) ? "x" : " ",
                    $job->name
                );
            }
        }

        /** @var string $result */
        $result = ob_get_clean();
        return $result;
    }

    public function getOutputFileName(string $folder): string
    {
        return "php://output";
    }
}
