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

    public function getSuites(string $folder): array
    {
        $suites = [];
        foreach ($this->finders as $finder) {
            $suites = array_unique(array_merge($suites, $finder->getSuites($folder)));
        }
        return array_values($suites);
    }
}
