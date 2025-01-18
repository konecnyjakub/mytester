<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\Group;
use MyTester\Attributes\Skip;
use MyTester\TestCase;

/**
 * @author Jakub Konečný
 */
#[Group("one")]
#[Group("test")]
final class TestCaseOne extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }

    public function testTwo(): void
    {
        $this->assertTrue(true);
    }

    #[Skip()]
    public function testThree(): void
    {
        $this->assertFalse(true);
    }
}
