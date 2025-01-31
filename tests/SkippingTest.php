<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\Skip;

/**
 * @author Jakub Konečný
 */
#[Skip]
final class SkippingTest extends TestCase
{
    public function testOne(): void
    {
        $this->assertFalse(true);
    }

    public function testTwo(): void
    {
        $this->assertFalse(true);
    }
}
