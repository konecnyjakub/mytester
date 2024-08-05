<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class JUnit
 *
 * @author Jakub Konečný
 */
#[TestSuite("Results formatter JUnit")]
final class JUnitTest extends TestCase
{
    public function testRender(): void
    {
        $outputFormatter = new JUnit();
        $testCase1 = new TestCaseOne();
        $testCase1->run();
        $outputFormatter->reportTestCase($testCase1);
        $testCase2 = new TestCaseTwo();
        $testCase2->run();
        $outputFormatter->reportTestCase($testCase2);
        $testCase3 = new TestCaseThree();
        $testCase3->run();
        $outputFormatter->reportTestCase($testCase3);
        $result = $outputFormatter->render(1);
        $result = str_replace(__DIR__, "/var/project/tests/ResultsFormatters", $result);
        $this->assertSame(file_get_contents(__DIR__ . "/junit_output.xml"), $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new JUnit();
        $this->assertSame("/var/project/junit.xml", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("/var/project/sub1/junit.xml", $outputFormatter->getOutputFileName("/var/project/sub1"));
    }
}
