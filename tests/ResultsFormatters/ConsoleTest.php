<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\TestSuite;
use MyTester\ConsoleColors;
use MyTester\DummyEventDispatcher;
use MyTester\Events\TestSuiteFinished;
use MyTester\Events\TestsFinished;
use MyTester\Events\TestsStarted;
use MyTester\TestCase;

/**
 * Test suite for class Console
 *
 * @author Jakub Konečný
 */
#[TestSuite("Results formatter console")]
final class ConsoleTest extends TestCase
{
    public function testRender(): void
    {
        $console = new ConsoleColors();
        $outputFormatter = new Console();
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
        $result = (string) preg_replace('/[0-9]+ ms\)/', "1 ms)", $result);
        $this->assertMatchesFile(__DIR__ . "/console_output.txt", $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new Console();
        $outputFormatter->setConsole(new ConsoleColors());
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project/sub1"));
    }
}
