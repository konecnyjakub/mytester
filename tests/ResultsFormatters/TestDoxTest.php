<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class Tap
 *
 * @author Jakub Konečný
 */
#[TestSuite("Results formatter TAP")]
final class TestDoxTest extends TestCase
{
    public function testRender(): void
    {
        $outputFormatter = new TestDox();
        $outputFormatter->reportTestsStarted([]);
        $testCase1 = new TestCaseOne();
        $testCase1->run();
        $outputFormatter->reportTestCaseFinished($testCase1);
        $testCase2 = new TestCaseTwo();
        $testCase2->run();
        $outputFormatter->reportTestCaseFinished($testCase2);
        $testCase3 = new TestCaseThree();
        $testCase3->run();
        $outputFormatter->reportTestCaseFinished($testCase3);
        $outputFormatter->reportTestsFinished([], 1);
        $result = $outputFormatter->render();
        $this->assertMatchesFile(__DIR__ . "/testdox_output.txt", $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new TestDox();
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project/sub1"));
        $outputFormatter->setOutputFileName("testdox.txt");
        $this->assertSame("/var/project/testdox.txt", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("/var/project/sub1/testdox.txt", $outputFormatter->getOutputFileName("/var/project/sub1"));
    }
}
