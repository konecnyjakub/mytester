<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class BaseTestSuitesFinder
 *
 * @author Jakub Konečný
 */
#[TestSuite("BaseTestSuitesFinderTest")]
final class BaseTestSuitesFinderTest extends TestCase
{
    public function testIsTestSuite(): void
    {
        $testSuitesFinder = new class extends BaseTestSuitesFinder
        {
            public function getSuites(string $folder): array
            {
                return [];
            }

            // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod
            public function isTestSuite(string $class): bool
            {
                return parent::isTestSuite($class);
            }
        };
        $this->assertFalse($testSuitesFinder->isTestSuite("abcdefg"));
        $this->assertFalse($testSuitesFinder->isTestSuite(TestSuite::class));
        $this->assertTrue($testSuitesFinder->isTestSuite(static::class));
    }
}
