<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteRobotLoader;

use MyTester\Annotations\Reader;
use MyTester\Attributes\Group;
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
#[Group("nette")]
#[Group("testSuitesFinders")]
final class TestSuitesFinderTest extends TestCase
{
    public function testGetSuites(): void
    {
        $testSuitesFinder = new TestSuitesFinder(Reader::create());

        $suites = $testSuitesFinder->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__ . "/../../"))
        );
        $this->assertCount(38, $suites);

        $suites = $testSuitesFinder->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__ . "/../../"), onlyGroups: ["test", ])
        );
        $this->assertCount(0, $suites);
    }
}
