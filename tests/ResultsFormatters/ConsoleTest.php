<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\TestSuite;
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
        $console = new \Nette\CommandLine\Console();
        $console->useColors(false);
        $outputFormatter = new Console();
        $outputFormatter->setTestsFolder(__DIR__);
        $outputFormatter->setConsole($console);
        $testCase1 = new TestCaseOne();
        $testCase1->run();
        $outputFormatter->reportTestCaseFinished($testCase1);
        $testCase2 = new TestCaseTwo();
        $testCase2->run();
        $outputFormatter->reportTestCaseFinished($testCase2);
        $testCase3 = new TestCaseThree();
        $testCase3->run();
        $outputFormatter->reportTestCaseFinished($testCase3);
        $result = $outputFormatter->render(1);
        $this->assertSame(file_get_contents(__DIR__ . "/console_output.txt"), $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new Console();
        $outputFormatter->setConsole(new \Nette\CommandLine\Console());
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("php://output", $outputFormatter->getOutputFileName("/var/project/sub1"));
    }
}
