<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use MyTester\TestsFolderProvider;
use MyTester\TestSuitesSelectionCriteria;

/**
 * Test suite for class PHPTTestSuitesFinder
 *
 * @author Jakub Konečný
 */
#[TestSuite("PHPTTestSuitesFinder")]
#[Group("testSuitesFinders")]
#[Group("phpt")]
final class PHPTTestSuitesFinderTest extends TestCase
{
    public function testGetSuites(): void
    {
        $testSuitesFinder = new PHPTTestSuitesFinder();

        $suites = $testSuitesFinder->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__))
        );
        $this->assertSame([PHPTTestCase::class, ], $suites);

        $suites = $testSuitesFinder->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__ . "/../.."))
        );
        $this->assertSame([PHPTTestCase::class, ], $suites);

        $suites = $testSuitesFinder->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__ . "/../Attributes"))
        );
        $this->assertSame([], $suites);
    }
}
