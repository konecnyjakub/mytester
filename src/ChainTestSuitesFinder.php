<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suites finder for {@see Tester}
 * Combines results from multiple finders
 *
 * @author Jakub Konečný
 */
final class ChainTestSuitesFinder implements ITestSuitesFinder
{
    /** @var ITestSuitesFinder[] */
    private array $finders;

    public function registerFinder(ITestSuitesFinder $finder): void
    {
        $this->finders[] = $finder;
    }

    public function getSuites(TestSuitesSelectionCriteria $criteria): array
    {
        $suites = [];
        foreach ($this->finders as $finder) {
            $suites = array_unique(array_merge($suites, $finder->getSuites($criteria)));
        }
        return array_values($suites);
    }
}
