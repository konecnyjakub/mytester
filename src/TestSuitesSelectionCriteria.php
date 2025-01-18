<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Criteria for selecting tests suites in {@see ITestSuitesFinder}
 *
 * @author Jakub Konečný
 */
final readonly class TestSuitesSelectionCriteria
{
    /**
     * @param string[] $onlyGroups
     * @param string[] $exceptGroups
     */
    public function __construct(
        public TestsFolderProvider $testsFolderProvider,
        public string $filenameSuffix = "Test.php",
        public array $onlyGroups = [],
        public array $exceptGroups = [],
    ) {
    }
}
