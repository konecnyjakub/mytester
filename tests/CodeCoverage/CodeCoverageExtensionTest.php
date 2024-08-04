<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\Engines\DummyEngine;
use MyTester\TestCase;

/**
 * Test suite for class CodeCoverageExtension
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage extension")]
final class CodeCoverageExtensionTest extends TestCase
{
    public function testSetupCodeCoverage(): void
    {
        $this->assertNoException(function () {
            $extension = new CodeCoverageExtension(new Collector());
            $extension->setupCodeCoverage();
        });
    }

    public function testReportCodeCoverage(): void
    {
        $this->assertNoException(function () {
            $extension = new CodeCoverageExtension(new Collector());
            $extension->reportCodeCoverage();
        });
        $this->assertNoException(function () {
            $collector = new Collector();
            $collector->registerEngine(new DummyEngine());
            $extension = new CodeCoverageExtension($collector);
            $extension->reportCodeCoverage();
        });
    }
}
