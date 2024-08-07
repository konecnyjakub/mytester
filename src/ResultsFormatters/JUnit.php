<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use DOMDocument;
use MyTester\Job;
use MyTester\JobResult;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * JUnit results formatter for Tester
 * Outputs the results into a file in JUnit format
 * @see https://www.ibm.com/docs/en/developer-for-zos/16.0?topic=formats-junit-xml-format
 * @see https://llg.cubic.org/docs/junit/
 *
 * @author Jakub Konečný
 */
final class JUnit extends AbstractResultsFormatter
{
    private string $baseFileName = "junit.xml";

    public function render(): string
    {
        if (!extension_loaded("dom")) {
            return "";
        }

        $totalTests = 0;
        $totalFailures = 0;
        $totalSkipped = 0;
        $totalWarnings = 0;
        $totalAssertions = 0;

        $document = new DOMDocument("1.0", "UTF-8");
        $document->formatOutput = true;

        $testSuites = $document->createElement("testsuites");
        $testSuites->setAttribute("name", "Project test suite by My Tester");
        $testSuites->setAttribute("time", (string) round($this->totalTime / 1000, 6));

        foreach ($this->testCases as $testCase) {
            $testSuiteTests = count($testCase->jobs);
            $totalTests += $testSuiteTests;
            $testSuiteFailures = count(array_filter($testCase->jobs, function (Job $job) {
                return $job->result === JobResult::FAILED;
            }));
            $totalFailures += $testSuiteFailures;
            $testSuiteSkipped = count(array_filter($testCase->jobs, function (Job $job) {
                return $job->result === JobResult::SKIPPED;
            }));
            $totalSkipped += $testSuiteSkipped;
            $testSuiteWarnings = count(array_filter($testCase->jobs, function (Job $job) {
                return $job->result === JobResult::WARNING;
            }));
            $totalWarnings += $testSuiteWarnings;
            $rc = new ReflectionClass($testCase::class);
            $testSuiteTime = 0;
            $testSuiteAssertions = 0;

            $testSuite = $document->createElement("testsuite");
            $testSuite->setAttribute("id", $rc->getName());
            $testSuite->setAttribute("name", $testCase->getSuiteName());
            $testSuite->setAttribute("file", (string) $rc->getFileName());
            $testSuite->setAttribute("tests", (string) $testSuiteTests);
            $testSuite->setAttribute("failures", (string) $testSuiteFailures);
            $testSuite->setAttribute("skipped", (string) $testSuiteSkipped);
            $testSuite->setAttribute("warnings", (string) $testSuiteWarnings);

            foreach ($testCase->jobs as $job) {
                $testSuiteTime += $job->totalTime;
                $testSuiteAssertions += $job->totalAssertions;
                $totalAssertions += $job->totalAssertions;
                $reflectionCallback = $this->createReflectionFromCallback($job->callback);

                $testCaseElement = $document->createElement("testcase");
                $testCaseElement->setAttribute("name", $job->name);
                $testCaseElement->setAttribute("class", $testCase::class);
                $testCaseElement->setAttribute("classname", (string) str_replace("\\", ".", $testCase::class));
                $testCaseElement->setAttribute("file", (string) $rc->getFileName());
                $testCaseElement->setAttribute("line", (string) $reflectionCallback->getStartLine());
                $testCaseElement->setAttribute("time", (string) round($job->totalTime / 1000, 6));
                $testCaseElement->setAttribute("assertions", (string) $job->totalAssertions);

                switch ($job->result) {
                    case JobResult::SKIPPED:
                        $skipped = $document->createElement("skipped");
                        if (is_string($job->skip)) {
                            $skipped->setAttribute("message", $job->skip);
                        }
                        $testCaseElement->appendChild($skipped);
                        break;
                    case JobResult::FAILED:
                        $message = $job->output . "\n\n" .
                            (string) $rc->getFileName() . ":" . $reflectionCallback->getStartLine();
                        $failure = $document->createElement("failure", $message);
                        $failure->setAttribute("type", "assert");
                        // each assertion failure is printed at a new line but \n is malformed as value of attribute
                        $failure->setAttribute("message", (string) str_replace("\n", " ", $job->output));
                        $testCaseElement->appendChild($failure);
                        break;
                }

                $testSuite->appendChild($testCaseElement);
            }

            $testSuite->setAttribute("time", (string) round($testSuiteTime / 1000, 6));
            $testSuite->setAttribute("assertions", (string) $testSuiteAssertions);
            $testSuites->appendChild($testSuite);
        }

        $testSuites->setAttribute("tests", (string) $totalTests);
        $testSuites->setAttribute("failures", (string) $totalFailures);
        $testSuites->setAttribute("skipped", (string) $totalSkipped);
        $testSuites->setAttribute("warnings", (string) $totalWarnings);
        $testSuites->setAttribute("assertions", (string) $totalAssertions);
        $document->appendChild($testSuites);

        return (string) $document->saveXML();
    }

    public function getOutputFileName(string $folder): string
    {
        return "$folder/{$this->baseFileName}";
    }

    private function createReflectionFromCallback(callable $callback): ReflectionFunctionAbstract
    {
        if (is_array($callback)) {
            return new ReflectionMethod($callback[0], $callback[1]);
        }
        /** @var string $callback */
        if (str_contains($callback, "::")) {
            return new ReflectionMethod(...explode("::", $callback));
        } else {
            return new ReflectionFunction($callback);
        }
    }
}
