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
            public function getSuites(string $folder): array
            {
                return [
                    "aaa", "bbb", "ccc",
                ];
            }
        });
        $testSuitesFinder->registerFinder(new class implements ITestSuitesFinder
        {
            public function getSuites(string $folder): array
            {
                return [
                    "aaa", "ddd",
                ];
            }
        });
        $this->assertSame(["aaa", "bbb", "ccc", "ddd", ], $testSuitesFinder->getSuites(""));
    }
}
