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
    public function testColor(): void
    {
        $consoleColors = new ConsoleColors();
        $this->assertSame("abc", $consoleColors->color("abc", "white"));
        $this->assertSame("abc", $consoleColors->color("abc"));
        $this->assertSame("abc", $consoleColors->color("abc", "non-existing"));

        $consoleColors->useColors = true;
        $this->assertSame("\x1b[1;37mabc\x1b[0m", $consoleColors->color("abc", "white"));
        $this->assertSame("abc", $consoleColors->color("abc"));
        $this->assertSame("abc", $consoleColors->color("abc", "non-existing"));

        $consoleColors->useColors = false;
        $this->assertSame("abc", $consoleColors->color("abc", "white"));
        $this->assertSame("abc", $consoleColors->color("abc"));
        $this->assertSame("abc", $consoleColors->color("abc", "non-existing"));
    }
}
