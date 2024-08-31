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
        $outputFormatter->reportTestsFinished([]);
        $result = $outputFormatter->render();
        $result = str_replace(__DIR__, "/var/project/tests/ResultsFormatters", $result);
        $result = (string) preg_replace('/time="0\.[0-9]+"/', 'time="0.001"', $result);
        $result = str_replace(
            "/var/project/tests/ResultsFormatters\\",
            "/var/project/tests/ResultsFormatters/",
            $result
        ); // this is necessary on Windows
        $this->assertMatchesFile(__DIR__ . "/junit_output.xml", $result);
    }

    public function testGetOutputFileName(): void
    {
        $outputFormatter = new JUnit();
        $this->assertSame("/var/project/junit.xml", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame("/var/project/sub1/junit.xml", $outputFormatter->getOutputFileName("/var/project/sub1"));
        $outputFormatter->setOutputFileName("junit_output.xml");
        $this->assertSame("/var/project/junit_output.xml", $outputFormatter->getOutputFileName("/var/project"));
        $this->assertSame(
            "/var/project/sub1/junit_output.xml",
            $outputFormatter->getOutputFileName("/var/project/sub1")
        );
    }
}
