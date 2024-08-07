<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Formatters;

use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\Report;
use MyTester\CodeCoverage\Engines\TestEngine;
use MyTester\TestCase;

/**
 * Test suite for class CoberturaFormatter
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage Cobertura formatter")]
final class CoberturaFormatterTest extends TestCase
{
    public function testRender(): void
    {
        $report = new Report((new TestEngine())->collect());
        $formatter = new CoberturaFormatter();
        $result = $formatter->render($report);
        /** @var string $result */
        $result = preg_replace('/(timestamp="\d+")/', 'timestamp="1"', $result);
        $result = str_replace($report->sourcePath, "/var/project/src/", $result);
        $this->assertMatchesFile(__DIR__ . "/cobertura.xml", $result);
    }

    public function testGetOutputFileName(): void
    {
        $formatter = new CoberturaFormatter();
        $this->assertSame("/var/project/coverage.xml", $formatter->getOutputFileName("/var/project"));
        $formatter->setOutputFileName("cobertura.xml");
        $this->assertSame("/var/project/cobertura.xml", $formatter->getOutputFileName("/var/project"));
    }
}
