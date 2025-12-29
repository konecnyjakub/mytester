<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;
use MyTester\ResultsFormatters\AbstractResultsFormatter;

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
        $resultsFormatter = new class extends AbstractResultsFormatter implements ConsoleAwareResultsFormatter
        {
            public ConsoleColors $console;

            public function setConsole(ConsoleColors $console): void
            {
                $this->console = $console;
            }

            public function render(): string
            {
                return "";
            }
        };
        $tester = new Tester(
            testSuitesSelectionCriteria: new TestSuitesSelectionCriteria(new TestsFolderProvider(__DIR__)),
            testSuitesFinder: new ChainTestSuitesFinder(),
            resultsFormatters: [$resultsFormatter]
        );

        $this->assertType(ConsoleColors::class, $resultsFormatter->console);
    }
}
