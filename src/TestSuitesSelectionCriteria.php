<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Criteria for selecting tests suites in {@see ITestSuitesFinder}
 *
 * @author Jakub Konečný
 * @internal
 */
final readonly class TestSuitesSelectionCriteria
{
    public function __construct(
        public TestsFolderProvider $testsFolderProvider,
        public string $filenameSuffix = ITestSuitesFinder::FILENAME_SUFFIX // @phpstan-ignore classConstant.deprecated
    ) {
    }
}
