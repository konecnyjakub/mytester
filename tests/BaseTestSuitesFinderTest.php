<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\ResultsFormatters\TestCaseOne;
use MyTester\ResultsFormatters\TestCaseThree;
use MyTester\ResultsFormatters\TestCaseTwo;
use ReflectionMethod;

/**
 * Test suite for class BaseTestSuitesFinder
 *
 * @author Jakub Konečný
 */
#[TestSuite("BaseTestSuitesFinderTest")]
#[Group("testSuitesFinders")]
final class BaseTestSuitesFinderTest extends TestCase
{
    public function testIsTestSuite(): void
    {
        $testSuitesFinder = new class extends BaseTestSuitesFinder
        {
            public function getSuites(TestSuitesSelectionCriteria $criteria): array
            {
                return [];
            }
        };
        $rm = new ReflectionMethod($testSuitesFinder, "isTestSuite");
        $this->assertFalse((bool) $rm->invoke($testSuitesFinder, "abcdefg"));
        $this->assertFalse((bool) $rm->invoke($testSuitesFinder, TestSuite::class));
        $this->assertTrue((bool) $rm->invoke($testSuitesFinder, self::class));
    }

    public function testApplyFilters(): void
    {
        $testSuitesFinder = new class ($this->annotationsReader) extends BaseTestSuitesFinder
        {
            public function __construct(Reader $reader)
            {
                $this->annotationsReader = $reader;
            }

            public function getSuites(TestSuitesSelectionCriteria $criteria): array
            {
                $testSuites = [TestCaseOne::class, TestCaseTwo::class, TestCaseThree::class, ];
                return $this->applyFilters($testSuites, $criteria);
            }
        };

        $this->assertSame(
            [TestCaseOne::class, ],
            $testSuitesFinder->getSuites(
                new TestSuitesSelectionCriteria(new TestsFolderProvider(""), onlyGroups: ["one", ])
            )
        );

        $this->assertSame(
            [TestCaseTwo::class, TestCaseThree::class, ],
            $testSuitesFinder->getSuites(
                new TestSuitesSelectionCriteria(new TestsFolderProvider(""), exceptGroups: ["one", ])
            )
        );

        $this->assertSame(
            [TestCaseOne::class, TestCaseTwo::class, TestCaseThree::class, ],
            $testSuitesFinder->getSuites(
                new TestSuitesSelectionCriteria(new TestsFolderProvider(""), onlyGroups: ["test", ])
            )
        );

        $this->assertSame(
            [],
            $testSuitesFinder->getSuites(
                new TestSuitesSelectionCriteria(new TestsFolderProvider(""), exceptGroups: ["test", ])
            )
        );
    }
}
