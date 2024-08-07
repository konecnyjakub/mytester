<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Results formatter for {@see Tester}
 *
 * @author Jakub Konečný
 * @internal
 */
interface IResultsFormatter
{
    public function setup(): void;

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
     * @param int $totalTime Total elapsed time in milliseconds
     */
    public function reportTestsFinished(array $testCases, int $totalTime): void;

    /**
     * Report that a {@see TestCase} was started
     */
    public function reportTestCaseStarted(TestCase $testCase): void;

    /**
     * Report results of one {@see TestCase}
     */
    public function reportTestCaseFinished(TestCase $testCase): void;

    /**
     * Generates and returns results of Tester run as string
     */
    public function render(): string;

    /**
     * Returns file name to which result of {@see self::render()} should written. The file does not have to exist yet
     * It can be an absolute path or standard output (or anything accepted by {@see fopen()})
     */
    public function getOutputFileName(string $folder): string;
}
