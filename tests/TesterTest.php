<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;
use MyTester\ResultsFormatters\AbstractResultsFormatter;
use Nette\CommandLine\Console;

/**
 * Test suite for class Tester
 *
 * @author Jakub Konečný
 */
#[TestSuite("Tester")]
final class TesterTest extends TestCase
{
    public function testSetUp(): void
    {
        $resultsFormatter = new class extends AbstractResultsFormatter implements IConsoleAwareResultsFormatter
        {
            public Console $console;
            public string $testsFolder = "";

            public function setConsole(Console $console): void
            {
                $this->console = $console;
            }

            public function render(): string
            {
                return "";
            }
        };
        $tester = new Tester(folder: __DIR__, resultsFormatter: $resultsFormatter);

        $this->assertType(Console::class, $resultsFormatter->console);
        $this->assertType(ChainTestSuitesFinder::class, $tester->testSuitesFinder);
        $rp = new \ReflectionProperty(ChainTestSuitesFinder::class, "finders");
        $rp->setAccessible(true);
        /** @var ITestSuitesFinder[] $finders */
        $finders = $rp->getValue($tester->testSuitesFinder);
        $this->assertArrayOfClass(ITestSuitesFinder::class, $finders);
        $this->assertCount(2, $finders);
    }

    public function testColors(): void
    {
        $resultsFormatter = new class extends AbstractResultsFormatter implements IConsoleAwareResultsFormatter
        {
            public Console $console;

            public function setConsole(Console $console): void
            {
                $this->console = $console;
            }

            public function render(): string
            {
                return "";
            }
        };
        $tester = new Tester(folder: __DIR__, resultsFormatter: $resultsFormatter);
        $rp = new \ReflectionProperty(Console::class, "useColors");
        $rp->setAccessible(true);

        $tester->useColors = true;
        $this->assertTrue($tester->useColors);
        $this->assertSame(true, $rp->getValue($resultsFormatter->console));

        $tester->useColors = false;
        $this->assertFalse($tester->useColors);
        $this->assertSame(false, $rp->getValue($resultsFormatter->console));
    }
}
