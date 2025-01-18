<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\Engines\DummyEngine;
use MyTester\Events\RunnerStarted;
use MyTester\Events\TestsFinished;
use MyTester\TestCase;

/**
 * Test suite for class CodeCoverageExtension
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage extension")]
#[Group("codeCoverage")]
#[Group("extensions")]
final class CodeCoverageExtensionTest extends TestCase
{
    public function testSetupCodeCoverage(): void
    {
        $this->assertNoException(function () {
            $extension = new CodeCoverageExtension(new Collector());
            $extension->onRunnerStarted(new RunnerStarted());
        });
    }

    public function testReportCodeCoverage(): void
    {
        $this->assertNoException(function () {
            $extension = new CodeCoverageExtension(new Collector());
            $extension->onTestsFinished(new TestsFinished());
        });
        $this->assertNoException(function () {
            $collector = new Collector();
            $collector->registerEngine(new DummyEngine());
            $extension = new CodeCoverageExtension($collector);
            $extension->onTestsFinished(new TestsFinished());
        });
    }
}
