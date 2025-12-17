<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Criteria for selecting tests suites in {@see TestSuitesFinder}
 *
 * @author Jakub Konečný
 */
final readonly class TestSuitesSelectionCriteria
{
    /**
     * @param string[] $onlyGroups
     * @param string[] $exceptGroups
     * @param string[] $exceptFolders
     */
    public function __construct(
        public TestsFolderProvider $testsFolderProvider,
        public string $filenameSuffix = "Test.php",
        public array $onlyGroups = [],
        public array $exceptGroups = [],
        public array $exceptFolders = [],
    ) {
    }
}
