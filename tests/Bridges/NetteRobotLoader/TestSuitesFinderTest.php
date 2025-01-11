<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteRobotLoader;

use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use MyTester\TestsFolderProvider;
use MyTester\TestSuitesSelectionCriteria;

/**
 * Test suite for class TestSuitesFinder
 *
 * @author Jakub Konečný
 */
#[TestSuite("TestSuitesFinder")]
final class TestSuitesFinderTest extends TestCase
{
    public function testGetSuites(): void
    {
        $testSuitesFinder = new TestSuitesFinder();
        $suites = $testSuitesFinder->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__ . "/../../"))
        );
        $this->assertCount(38, $suites);
    }
}
