<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Formatters;

use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\Engines\DummyEngine;
use MyTester\CodeCoverage\Report;
use MyTester\TestCase;

/**
 * Test suite for class PercentFormatter
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage percent formatter")]
class PercentFormatterTest extends TestCase
{
    public function testRender(): void
    {
        $report = new Report((new DummyEngine())->collect());
        $formatter = new PercentFormatter();
        $result = $formatter->render($report);
        $this->assertSame("Calculating code coverage... 62% covered\n", $result);
    }
}
