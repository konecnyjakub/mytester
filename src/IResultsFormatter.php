<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Results formatter for {@see Tester}
 *
 * @author Jakub Konečný
 */
interface IResultsFormatter
{
    /**
     * Report that we started running tests
     *
     * @param TestCase[] $testCases
     */
    public function reportTestsStarted(array $testCases): void;

    /**
     * Report that all tests finished
     *
     * @param TestCase[] $testCases
     */
    public function reportTestsFinished(array $testCases): void;

    /**
     * Report that a {@see TestCase} was started
     */
    public function reportTestCaseStarted(TestCase $testCase): void;

    /**
     * Report results of one {@see TestCase}
     */
    public function reportTestCaseFinished(TestCase $testCase): void;

    /**
     * Generates results of Tester run and outputs it to set file/console
     *
     * @param string $outputFolder Where file with output should be created (if writing to a file)
     */
    public function outputResults(string $outputFolder): void;

    /**
     * Returns file name to which result should written. The file does not have to exist yet
     * It can be an absolute path or standard output (or anything accepted by {@see fopen()})
     */
    public function getOutputFileName(string $folder): string;

    /**
     * Set file name for output that should be used instead of the default one
     */
    public function setOutputFileName(string $baseFileName): void;
}
