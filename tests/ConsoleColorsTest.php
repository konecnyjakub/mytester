<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class ConsoleColors
 *
 * @author Jakub Konečný
 */
#[TestSuite("ConsoleColors")]
final class ConsoleColorsTest extends TestCase
{
    public function testUseColors(): void
    {
        $consoleColors = new ConsoleColors();
        $this->assertFalse($consoleColors->useColors);

        $consoleColors->useColors = true;
        $this->assertTrue($consoleColors->useColors);

        $consoleColors->useColors = false;
        $this->assertFalse($consoleColors->useColors);
    }

    public function testColor(): void
    {
        $consoleColors = new ConsoleColors();
        $this->assertSame("abc", $consoleColors->color("abc", "white"));

        $consoleColors->useColors = true;
        $this->assertSame("\x1b[1;37mabc\x1b[0m", $consoleColors->color("abc", "white"));

        $consoleColors->useColors = false;
        $this->assertSame("abc", $consoleColors->color("abc", "white"));
    }
}
