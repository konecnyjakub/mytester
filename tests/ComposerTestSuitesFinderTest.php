<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class ComposerTestSuitesFinder
 *
 * @author Jakub Konečný
 */
#[TestSuite("ComposerTestSuitesFinder")]
#[Group("testSuitesFinders")]
final class ComposerTestSuitesFinderTest extends TestCase
{
    public function testGetSuites(): void
    {
        $testSuitesFinder = new ComposerTestSuitesFinder(Reader::create());

        $suites = $testSuitesFinder->getSuites(new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__)));
        $this->assertCount(43, $suites);

        $suites = $testSuitesFinder->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__), onlyGroups: ["test", ])
        );
        $this->assertCount(0, $suites);
    }
}
