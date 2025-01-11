<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class ChainTestSuitesFinder
 *
 * @author Jakub Konečný
 */
#[TestSuite("ChainTestSuitesFinderTest")]
final class ChainTestSuitesFinderTest extends TestCase
{
    public function testGetSuites(): void
    {
        $testSuitesFinder = new ChainTestSuitesFinder();
        $testSuitesFinder->registerFinder(new class implements ITestSuitesFinder
        {
            public function getSuites(TestSuitesSelectionCriteria $criteria): array
            {
                return [
                    \stdClass::class, \Closure::class, \Generator::class,
                ];
            }
        });
        $testSuitesFinder->registerFinder(new class implements ITestSuitesFinder
        {
            public function getSuites(TestSuitesSelectionCriteria $criteria): array
            {
                return [
                    \stdClass::class, \Fiber::class,
                ];
            }
        });
        $this->assertSame(
            [\stdClass::class, \Closure::class, \Generator::class, \Fiber::class, ],
            $testSuitesFinder->getSuites(new TestSuitesSelectionCriteria(new TestsFolderProvider("")))
        );
    }
}
