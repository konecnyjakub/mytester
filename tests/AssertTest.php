<?php

declare(strict_types=1);

namespace MyTester;

use stdClass;

/**
 * Test suite for class Assert
 *
 * @author Jakub Konečný
 */
final class AssertTest extends TestCase
{
    /**
     * Test assertion functions
     */
    public function testAssertion(): void
    {
        $this->assertSame("abc", "abc");
        $this->assertNotSame("abc", "def");
        $this->assertTrue(true);
        $this->assertTruthy(1);
        $this->assertFalse(false);
        $this->assertFalsey(0);
        $this->assertNull(null);
        $this->assertNotNull("abc");
        $testArray = ["abc"];
        $this->assertContains("abc", $testArray);
        $this->assertNotContains("def", $testArray);
        $this->assertCount(1, $testArray);
        $this->assertNotCount(0, $testArray);
        $this->assertType("array", $testArray);
        $this->assertType(__CLASS__, $this);
        $this->assertType("string", "abc");
        $this->assertType("bool", true);
        $this->assertType("int", 42);
        $this->assertType("null", null);
        $this->assertType("object", new stdClass());
        $this->assertType("scalar", 42);
        $this->assertThrowsException(function () {
            throw new \RuntimeException("abc", 1);
        }, \RuntimeException::class);
        $this->assertThrowsException(function () {
            throw new \RuntimeException("abc");
        }, \RuntimeException::class);
        $this->assertThrowsException(function () {
            throw new \RuntimeException("abc", 1);
        }, \RuntimeException::class);
        $this->assertThrowsException(function () {
            throw new \RuntimeException("abc");
        }, \RuntimeException::class, "abc");
        $this->assertThrowsException(function () {
            throw new \RuntimeException("abc", 1);
        }, \RuntimeException::class, "abc");
        $this->assertThrowsException(function () {
            throw new \RuntimeException("abc", 1);
        }, \RuntimeException::class, "abc", 1);
    }

    /**
     * Test custom assertions and custom messages
     */
    public function testCustomAssertion(): void
    {
        $this->assert("5 > 2", "5 is not greater that 2.");
        $this->assert("5 >= 2", "5 is not greater or equal to 2.");
        $this->assert("2 < 5", "2 is not lesser than 5.");
        $this->assert("2 <= 5", "2 is not lesser or equal to 5.");
        $this->assert("abc != def", "abc is def.");
    }

    public function testShowStringOrArray(): void
    {
        $text = "abc";
        $this->assertSame($text, $this->showStringOrArray($text));
        $this->assertSame("(array)", $this->showStringOrArray([]));
    }
}
