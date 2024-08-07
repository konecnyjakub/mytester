<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\JobResult;

/**
 * TestDox results formatter for Tester
 * Outputs the results to console/standard output in TestDox format
 *
 * @author Jakub Konečný
 * @internal
 */
final class TestDox extends AbstractResultsFormatter
{
    public function render(): string
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
}
