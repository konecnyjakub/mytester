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
final class TAPTest extends TestCase
{
    public function testRender(): void
    {
        $outputFormatter = new Tap();
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
        $this->assertSame(file_get_contents(__DIR__ . "/tap_output.txt"), $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new Tap();
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project/sub1"));
    }
}