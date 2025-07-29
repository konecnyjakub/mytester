<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use DOMDocument;
use MyTester\AssertionFailedException;
use MyTester\IResultsFormatter;
use MyTester\Job;
use MyTester\JobResult;
use MyTester\TAssertions;
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
    protected string $baseFileName = "junit.xml";

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

        foreach ($this->testSuites as $testSuite) {
            $testSuiteTests = count($testSuite->jobs);
            $totalTests += $testSuiteTests;
            $testSuiteFailures = count(array_filter($testSuite->jobs, static function (Job $job) {
                return $job->result === JobResult::FAILED;
            }));
            $totalFailures += $testSuiteFailures;
            $testSuiteSkipped = count(array_filter($testSuite->jobs, static function (Job $job) {
                return $job->result === JobResult::SKIPPED;
            }));
            $totalSkipped += $testSuiteSkipped;
            $testSuiteWarnings = count(array_filter($testSuite->jobs, static function (Job $job) {
                return $job->result === JobResult::WARNING;
            }));
            $totalWarnings += $testSuiteWarnings;
            $rc = new ReflectionClass($testSuite::class);
            $testSuiteTime = 0;
            $testSuiteAssertions = 0;

            $testSuiteElement = $document->createElement("testsuite");
            $testSuiteElement->setAttribute("id", $rc->getName());
            $testSuiteElement->setAttribute("name", $testSuite->getSuiteName());
            $testSuiteElement->setAttribute("file", (string) $rc->getFileName());
            $testSuiteElement->setAttribute("tests", (string) $testSuiteTests);
            $testSuiteElement->setAttribute("failures", (string) $testSuiteFailures);
            $testSuiteElement->setAttribute("skipped", (string) $testSuiteSkipped);
            $testSuiteElement->setAttribute("warnings", (string) $testSuiteWarnings);

            foreach ($testSuite->jobs as $job) {
                $testSuiteTime += $job->totalTime;
                $testSuiteAssertions += $job->totalAssertions;
                $totalAssertions += $job->totalAssertions;
                /** @var callable&array{0: class-string, 1: string} $callback */
                $callback = $job->callback;
                $reflectionCallback = $this->createReflectionFromCallback($callback);

                $testCaseElement = $document->createElement("testcase");
                $testCaseElement->setAttribute("name", $job->name);
                $testCaseElement->setAttribute("class", $testSuite::class);
                $testCaseElement->setAttribute("classname", (string) str_replace("\\", ".", $testSuite::class));
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
                            (string) $rc->getFileName() . ":" .
                            $this->getFailureLine($job->exception, $reflectionCallback);
                        $failure = $document->createElement("failure", $message);
                        $failure->setAttribute("type", "assert");
                        $failure->setAttribute("message", $job->output);
                        $testCaseElement->appendChild($failure);
                        break;
                }

                $testSuiteElement->appendChild($testCaseElement);
            }

            $testSuiteElement->setAttribute("time", (string) round($testSuiteTime / 1000, 6));
            $testSuiteElement->setAttribute("assertions", (string) $testSuiteAssertions);
            $testSuites->appendChild($testSuiteElement);
        }

        $testSuites->setAttribute("tests", (string) $totalTests);
        $testSuites->setAttribute("failures", (string) $totalFailures);
        $testSuites->setAttribute("skipped", (string) $totalSkipped);
        $testSuites->setAttribute("warnings", (string) $totalWarnings);
        $testSuites->setAttribute("assertions", (string) $totalAssertions);
        $document->appendChild($testSuites);

        return (string) $document->saveXML();
    }

    public function setOutputFileName(string $baseFileName): void
    {
        $this->baseFileName = $baseFileName;
    }

    /**
     * @param callable&(array{0: class-string, 1: string}|string) $callback
     */
    private function createReflectionFromCallback(callable $callback): ReflectionFunctionAbstract
    {
        if (is_array($callback)) {
            return new ReflectionMethod($callback[0], $callback[1]);
        }
        if (is_string($callback) && str_contains($callback, "::")) {
            return new ReflectionMethod(...explode("::", $callback));
        } else {
            return new ReflectionFunction($callback);
        }
    }

    private function getFailureLine(\Throwable|null $exception, ReflectionFunctionAbstract $reflection): int
    {
        if ($exception instanceof AssertionFailedException) {
            /**
             * the exception is thrown in {@see TAssertions::testResult()} so we check where it was called from
             */
            if (isset($exception->getTrace()[1])) {
                $trace = $exception->getTrace()[1];
                if (isset($trace["line"])) {
                    return $trace["line"];
                }
            }
        }
        return (int) $reflection->getStartLine();
    }
}
