<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\DummyEngine;
use MyTester\Attributes\TestSuite;
use stdClass;

/**
 * Test suite for class Assert
 *
 * @author Jakub Konečný
 */
#[TestSuite("Assertions")]
final class AssertTest extends TestCase
{
    /**
     * Test assertion functions
     */
    public function testAssertion(): void
    {
        $this->assertSame("abc", "abc");
        $this->assertNotSame("abc", "def");
        $this->assertGreaterThan(1, 2);
        $this->assertLessThan(2, 1);
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
        $this->assertNoException(function () {
            time();
        });
        $this->assertOutput(function () {
            echo "abc";
        }, "abc");
        $this->assertMatchesRegExp('/abc/', "1abc2");
        $this->assertMatchesFile(__DIR__ . "/test.txt", "abc\n");
        $this->assertArrayOfClass(
            \stdClass::class,
            [new stdClass(), new stdClass(), ]
        );
    }

    /**
     * Test failures of assertions
     */
    public function testAssertionFailures(): void
    {
        $this->assertOutput(function () {
            $this->testResult("abc", false);
        }, "Test 1 failed. abc\n");
        $this->assertOutput(function () {
            $this->assert(false);
        }, "Test 3 failed. The assertion is not true.\n");
        $this->assertOutput(function () {
            $this->assert(false, "abc");
        }, "Test 5 failed. abc\n");
        $this->assertOutput(function () {
            $this->assertSame("abc", "def");
        }, "Test 7 failed. The value is not 'abc' but 'def'.\n");
        $this->assertOutput(function () {
            $this->assertNotSame("abc", "abc");
        }, "Test 9 failed. The value is 'abc'.\n");
        $this->assertOutput(function () {
            $this->assertGreaterThan(2, 1);
        }, "Test 11 failed. 1 is not greater than 2.\n");
        $this->assertOutput(function () {
            $this->assertLessThan(1, 2);
        }, "Test 13 failed. 2 is not less than 1.\n");
        $this->assertOutput(function () {
            $this->assertTrue(false);
        }, "Test 15 failed. The value is not true.\n");
        $this->assertOutput(function () {
            $this->assertTruthy(false);
        }, "Test 17 failed. The expression is not true.\n");
        $this->assertOutput(function () {
            $this->assertFalse(true);
        }, "Test 19 failed. The value is not false.\n");
        $this->assertOutput(function () {
            $this->assertFalsey(true);
        }, "Test 21 failed. The expression is not false.\n");
        $this->assertOutput(function () {
            $this->assertNull("abc");
        }, "Test 23 failed. The value is not null.\n");
        $this->assertOutput(function () {
            $this->assertNotNull(null);
        }, "Test 25 failed. The value is null.\n");
        $this->assertOutput(function () {
            $this->assertContains("abc", "def");
        }, "Test 27 failed. abc is not in the variable.\n");
        $this->assertOutput(function () {
            $this->assertContains("abc", [1, 2, ]);
        }, "Test 29 failed. 'abc' is not in the variable.\n");
        $this->assertOutput(function () {
            $this->assertContains(["abc", ], "abc");
        }, "Test 31 failed. array (\n  0 => 'abc',\n) is not in the variable.\n");
        $this->assertOutput(function () {
            $this->assertNotContains("abc", "abc");
        }, "Test 33 failed. abc is in the variable.\n");
        $this->assertOutput(function () {
            $this->assertNotContains("abc", ["abc", "def", ]);
        }, "Test 35 failed. 'abc' is in the variable.\n");
        $this->assertOutput(function () {
            $this->assertNotContains(["abc", "def", ], "abc");
        }, "Test 37 failed. array (\n  0 => 'abc',\n  1 => 'def',\n) is not in the variable.\n");
        $this->assertOutput(function () {
            $this->assertCount(1, ["abc", "def", ]);
        }, "Test 39 failed. Count of the variable is 2.\n");
        $this->assertOutput(function () {
            $this->assertNotCount(2, ["abc", "def", ]);
        }, "Test 41 failed. Count of the variable is 2.\n");
        $this->assertOutput(function () {
            $this->assertType("string", ["abc", "def", ]);
        }, "Test 43 failed. The variable is array.\n");
        $this->assertOutput(function () {
            $this->assertType(static::class, new stdClass());
        }, "Test 45 failed. The variable is instance of stdClass.\n");
        $this->assertOutput(function () {
            $this->assertThrowsException(function () {
            }, \Exception::class);
        }, "Test 47 failed. The code does not throw any exception.\n");
        $this->assertOutput(function () {
            $this->assertThrowsException(function () {
                throw new \RuntimeException();
            }, \LogicException::class);
        }, "Test 49 failed. The code does not throw LogicException but RuntimeException.\n");
        $this->assertOutput(function () {
            $this->assertThrowsException(function () {
                throw new \RuntimeException("def");
            }, \RuntimeException::class, "abc");
        }, "Test 51 failed. The code does not throw an exception with message 'abc' but 'def'.\n");
        $this->assertOutput(function () {
            $this->assertThrowsException(function () {
                throw new \RuntimeException("abc", 2);
            }, \RuntimeException::class, "abc", 1);
        }, "Test 53 failed. The code does not throw an exception with code 1 but 2.\n");
        $this->assertOutput(function () {
            $this->assertNoException(function () {
                throw new \RuntimeException();
            });
        }, "Test 55 failed. No exception was expected but RuntimeException was thrown.\n");
        $this->assertOutput(function () {
            $this->assertMatchesRegExp('/abc/', "def");
        }, "Test 57 failed. The string does not match regular expression.\n");
        $this->assertOutput(function () {
            $this->assertArrayOfClass(
                \stdClass::class,
                [new stdClass(), new DummyEngine(), "abc", ]
            );
        }, "Test 59 failed. The array does not contain only instances of stdClass.\n");
        $this->assertOutput(function () {
            $this->assertMatchesFile(__DIR__ . "/non_existing.txt", "");
        }, "Test 61 failed. File " . __DIR__ . "/non_existing.txt could not be loaded.\n");
        $this->assertOutput(function () {
            $this->assertMatchesFile(__DIR__ . "/test.txt", "");
        }, "Test 63 failed. The value is not 'abc\n' but ''.\n");
        $this->assertOutput(function () {
            $this->assertArrayOfClass(
                \stdClass::class,
                []
            );
        }, "Test 65 failed. The array is empty.\n");
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
        $this->assertSame("'$text'", $this->showStringOrArray($text));
        $this->assertSame("array (\n)", $this->showStringOrArray([]));
    }
}
