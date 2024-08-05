<?php
declare(strict_types=1);

namespace MyTester\OutputFormatters;

use MyTester\TestCase;

final class TestCaseThree extends TestCase
{
    public function testOne(): void
    {
        $this->markTestSkipped("abc");
    }

    /**
     * @author Jakub Konečný
     */
    public function testTwo(): void
    {
        $this->assertTrue(true);
        $this->markTestIncomplete();
    }
}
