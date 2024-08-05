<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\TestCase;

/**
 * @author Jakub Konečný
 */
final class TestCaseTwo extends TestCase
{
    public function testOne(): void
    {
        $this->assertTrue(true);
    }

    public function testTwo(): void
    {
        $this->assertTrue(false);
    }

    public function testThree(): void
    {
    }
}
