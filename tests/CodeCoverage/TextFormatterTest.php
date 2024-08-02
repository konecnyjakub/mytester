<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class TextFormatter
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage text formatter")]
class TextFormatterTest extends TestCase
{
    public function testRender(): void
    {
        $report = new Report((new DummyEngine())->collect());
        $formatter = new TextFormatter();
        $result = $formatter->render($report);
        $this->assertSame(file_get_contents(__DIR__ . "/coverage.txt"), $result);
    }

    public function testGetOutputFileName(): void
    {
        $formatter = new TextFormatter();
        $this->assertSame("/var/project/coverage.txt", $formatter->getOutputFileName("/var/project"));
        $formatter->setOutputFileName("custom.txt");
        $this->assertSame("/var/project/custom.txt", $formatter->getOutputFileName("/var/project"));
    }
}
