<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Formatters;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\Engines\DummyEngine;
use MyTester\CodeCoverage\Report;
use MyTester\TestCase;

/**
 * Test suite for class TextFormatter
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage text formatter")]
#[Group("codeCoverage")]
#[Group("codeCoverageFormatters")]
class TextFormatterTest extends TestCase
{
    public function testRender(): void
    {
        $report = new Report((new DummyEngine())->collect());
        $formatter = new TextFormatter();
        $result = $formatter->render($report);
        $this->assertMatchesFile(__DIR__ . "/coverage.txt", $result);
    }

    public function testGetOutputFileName(): void
    {
        $formatter = new TextFormatter();
        $this->assertSame("/var/project/coverage.txt", $formatter->getOutputFileName("/var/project"));
        $formatter->setOutputFileName("custom.txt");
        $this->assertSame("/var/project/custom.txt", $formatter->getOutputFileName("/var/project"));
    }
}
