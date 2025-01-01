<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class ComposerTestSuitesFinder
 *
 * @author Jakub Konečný
 */
#[TestSuite("ComposerTestSuitesFinder")]
final class ComposerTestSuitesFinderTest extends TestCase
{
    public function testGetSuites(): void
    {
        $testSuitesFinder = new ComposerTestSuitesFinder();
        $suites = $testSuitesFinder->getSuites(__DIR__);
        $this->assertCount(37, $suites);
    }
}
