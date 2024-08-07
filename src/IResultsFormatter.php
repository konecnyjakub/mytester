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
     * Report results of one {@see TestCase}
     */
    public function reportTestCase(TestCase $testCase): void;

    /**
     * Generates and returns results of Tester run as string
     *
     * @param int $totalTime Total elapsed time in milliseconds
     */
    public function render(int $totalTime): string;

    /**
     * Returns file name to which result of {@see self::render()} should written. The file does not have to exist yet
     * It can be an absolute path or standard output (or anything accepted by {@see fopen()})
     */
    public function getOutputFileName(string $folder): string;
}
