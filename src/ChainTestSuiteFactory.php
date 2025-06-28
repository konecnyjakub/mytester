<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite factory for {@see Tester}
 *
 * Tries all available factories until one is able to create the test suite
 *
 * @author Jakub Konečný
 */
final class ChainTestSuiteFactory implements ITestSuiteFactory
{
    /** @var ITestSuiteFactory[] */
    private array $factories = [];

    public function registerFactory(ITestSuiteFactory $factory): void
    {
        $this->factories[] = $factory;
    }

    public function create(string $className): ?TestCase
    {
        foreach ($this->factories as $factory) {
            $testSuite = $factory->create($className);
            if ($testSuite !== null) {
                return $testSuite;
            }
        }
        return null;
    }
}
