<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use MyTester\TestCase;
use ValueError;

/**
 * Test suite for class Repeat
 *
 * @author Jakub Konečný
 */
#[TestSuite("Repeat")]
#[Group("attributes")]
final class RepeatTest extends TestCase
{
    public function testValidValues(): void
    {
        $this->assertNoException(static function () {
            new Repeat(2);
        });

        $this->assertNoException(static function () {
            new Repeat(PHP_INT_MAX);
        });

        $this->assertThrowsException(static function () {
            new Repeat(1);
        }, ValueError::class);

        $this->assertThrowsException(static function () {
            new Repeat(0);
        }, ValueError::class);

        $this->assertThrowsException(static function () {
            new Repeat(1);
        }, ValueError::class);

        $this->assertThrowsException(static function () {
            new Repeat(PHP_INT_MIN);
        }, ValueError::class);
    }
}
