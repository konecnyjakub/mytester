<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use Konecnyjakub\EventDispatcher\DummyEventDispatcher;
use MyTester\Attributes\TestSuite;
use MyTester\ConsoleColors;
use MyTester\Events\TestSuiteFinished;
use MyTester\Events\TestsFinished;
use MyTester\Events\TestsStarted;
use MyTester\TestCase;

/**
 * Test suite for class Tap
 *
 * @author Jakub Konečný
 */
#[TestSuite("Results formatter TestDox")]
final class TestDoxTest extends TestCase
{
    public function testRender(): void
    {
        $console = new ConsoleColors();
        $outputFormatter = new TestDox();
        $outputFormatter->setConsole($console);
        $outputFormatter->reportTestsStarted(new TestsStarted([]));
        $testCase1 = new TestCaseOne();
        $testCase1->setEventDispatcher(new DummyEventDispatcher());
        $testCase1->run();
        $outputFormatter->reportTestCaseFinished(new TestSuiteFinished($testCase1));
        $testCase2 = new TestCaseTwo();
        $testCase2->setEventDispatcher(new DummyEventDispatcher());
        $testCase2->run();
        $outputFormatter->reportTestCaseFinished(new TestSuiteFinished($testCase2));
        $testCase3 = new TestCaseThree();
        $testCase3->setEventDispatcher(new DummyEventDispatcher());
        $testCase3->run();
        $outputFormatter->reportTestCaseFinished(new TestSuiteFinished($testCase3));
        $outputFormatter->reportTestsFinished(new TestsFinished([]));
        $result = $outputFormatter->render();
        $this->assertMatchesFile(__DIR__ . "/testdox_output.txt", $result);
    }

    public function testGetOutputFileName(): void
    {
        $console = new ConsoleColors();
        $outputFormatter = new TestDox();
        $outputFormatter->setConsole($console);
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project/sub1"));
        $outputFormatter->setOutputFileName("testdox.txt");
        $this->assertSame("/var/project/testdox.txt", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("/var/project/sub1/testdox.txt", $outputFormatter->getOutputFileName("/var/project/sub1"));
    }
}
