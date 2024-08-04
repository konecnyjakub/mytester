<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Engines;

use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class PcovEngine
 *
 * @author Jakub Konečný
 */
#[TestSuite("pcov engine")]
final class PcovEngineTest extends TestCase
{
    public function testGetName(): void
    {
        $engine = new PcovEngine();
        $this->assertSame("pcov", $engine->getName());
    }

    #[Skip(["extension" => "pcov"])]
    public function testIsAvailable(): void
    {
        $engine = new PcovEngine();
        $this->assertTrue($engine->isAvailable());
    }
}
