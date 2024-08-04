<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage\Engines;

use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class XDebugEngine
 *
 * @author Jakub Konečný
 */
#[TestSuite("XDebug engine")]
final class XDebugEngineTest extends TestCase
{
    public function testGetName(): void
    {
        $engine = new XDebugEngine();
        $this->assertSame("XDebug", $engine->getName());
    }

    #[Skip(["extension" => "xdebug"])]
    public function testIsAvailable(): void
    {
        $engine = new XDebugEngine();
        $this->assertTrue($engine->isAvailable());
    }
}
