<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\TestSuite;
use MyTester\DummyEventDispatcher;
use MyTester\Events\TestCaseFinished;
use MyTester\Events\TestsFinished;
use MyTester\Events\TestsStarted;
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
        $testCase1 = new TestCaseOne();
        $testCase1->setEventDispatcher(new DummyEventDispatcher());
        $testCase2 = new TestCaseTwo();
        $testCase2->setEventDispatcher(new DummyEventDispatcher());
        $testCase3 = new TestCaseThree();
        $testCase3->setEventDispatcher(new DummyEventDispatcher());
        $outputFormatter = new Tap();
        $outputFormatter->reportTestsStarted(new TestsStarted([$testCase1, $testCase2, $testCase3, ]));
        $testCase1->run();
        $outputFormatter->reportTestCaseFinished(new TestCaseFinished($testCase1));
        $testCase2->run();
        $outputFormatter->reportTestCaseFinished(new TestCaseFinished($testCase2));
        $testCase3->run();
        $outputFormatter->reportTestCaseFinished(new TestCaseFinished($testCase3));
        $outputFormatter->reportTestsFinished(new TestsFinished([]));
        $result = $outputFormatter->render();
        $this->assertMatchesFile(__DIR__ . "/tap_output.txt", $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new Tap();
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project/sub1"));
        $outputFormatter->setOutputFileName("tap.txt");
        $this->assertSame("/var/project/tap.txt", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("/var/project/sub1/tap.txt", $outputFormatter->getOutputFileName("/var/project/sub1"));
    }
}
