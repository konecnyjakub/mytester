<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ChainTestsSuitesFinder implements ITestsSuitesFinder
{
    /** @var ITestsSuitesFinder[] */
    private array $finders;

    public function registerFinder(ITestsSuitesFinder $finder): void
    {
        $this->finders[] = $finder;
    }

    public function getSuites(string $folder): array
    {
        $suites = [];
        foreach ($this->finders as $finder) {
            $suites = array_unique(array_merge($suites, $finder->getSuites($folder)));
        }
        return $suites;
    }
}
