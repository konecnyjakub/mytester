<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\IResultsFormatter;

/**
 * Base results formatter for Tester
 *
 * @author Jakub Konečný
 * @internal
 */
abstract class AbstractResultsFormatter implements IResultsFormatter
{
    /** @var TestCase[] */
    protected array $testCases = [];

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

    /**
     * @inheritDoc
     */
    public function getOutputFileName(string $folder): string
    {
        return "php://output";
    }
}
