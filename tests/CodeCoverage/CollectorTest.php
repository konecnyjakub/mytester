<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\CodeCoverageException as Exception;
use MyTester\CodeCoverage\Engines\DummyEngine;
use MyTester\TestCase;

/**
 * Test suite for class Collector
 *
 * @author Jakub Konečný
 */
#[TestSuite("Code coverage collector")]
#[Group("codeCoverage")]
final class CollectorTest extends TestCase
{
    public function testStart(): void
    {
        $this->assertThrowsException(function () {
            $collector = new Collector();
            $collector->start();
        }, Exception::class, "No code coverage engine is available.", Exception::NO_ENGINE_AVAILABLE);

        $collector = new Collector();
        $collector->registerEngine(new DummyEngine());
        $collector->start();
    }

    public function testFinish(): void
    {
        $this->assertThrowsException(function () {
            $collector = new Collector();
            $collector->finish();
        }, Exception::class, "Code coverage collector has not been started.", Exception::COLLECTOR_NOT_STARTED);

        $collector = new Collector();
        $collector->registerEngine(new DummyEngine());
        $collector->start();
        $result = $collector->finish();
        $this->assertType(Report::class, $result);
    }

    public function testGetEngineName(): void
    {
        $this->assertThrowsException(function () {
            $collector = new Collector();
            $collector->getEngineName();
        }, Exception::class, "No code coverage engine is available.", Exception::NO_ENGINE_AVAILABLE);

        $collector = new Collector();
        $collector->registerEngine(new DummyEngine());
        $this->assertSame("dummy", $collector->getEngineName());
    }
}
