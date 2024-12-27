<?php
declare(strict_types=1);

namespace MyTester;

use ArrayObject;
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
        $this->assertTriggersDeprecation(function () {
            trigger_error("test", E_USER_DEPRECATED);
        });
        $this->assertTriggersDeprecation(function () {
            trigger_error("test", E_USER_DEPRECATED);
        }, "test");
        if (version_compare(PHP_VERSION, "8.4.0") >= 0) {
            $this->assertTriggersDeprecation(function () {
                $this->deprecatedMethod(); // @phpstan-ignore method.deprecated
            });
            $this->assertTriggersDeprecation(function () {
                $this->deprecatedMethod(); // @phpstan-ignore method.deprecated
            }, "Method MyTester\AssertTest::deprecatedMethod() is deprecated, test");
        }
        $this->assertTriggersNoDeprecation(function () {
        });
        $this->assertArrayHasKey("abc", ["abc" => 1, "def" => 2, ]);
        $this->assertArrayHasKey(2, [0, 5, 10, ]);
        $arrayObject = new ArrayObject();
        $arrayObject->offsetSet("test", "abc");
        $this->assertArrayHasKey("test", $arrayObject);
        $this->assertArrayNotHasKey("xyz", ["abc" => 1, "def" => 2, ]);
        $this->assertArrayNotHasKey(5, [0, 5, 10, ]);
        $this->assertArrayNotHasKey("abc", $arrayObject);
        $this->assertSameSize([], []);
        $this->assertSameSize([0, 1, 2, ], [3, 4, 5, ]);
        $this->assertSameSize(new ArrayObject(), new ArrayObject());
        $this->assertSameSize($arrayObject, $arrayObject);
        $this->assertSameSize($arrayObject, [0, ]);
        $this->assertSameSize([0, ], $arrayObject);
        $this->assertSameSize(new ArrayObject(), []);
        $this->assertSameSize([], new ArrayObject());
    }

    /**
     * Test failures of assertions
     */
    public function testAssertionFailures(): void
    {
        $this->assertThrowsException(function () {
            $this->testResult("abc", false);
        }, AssertionFailedException::class, "Test 1 failed. abc");
        $this->assertThrowsException(function () {
            $this->assert(false);
        }, AssertionFailedException::class, "Test 3 failed. The assertion is not true.");
        $this->assertThrowsException(function () {
            $this->assert(false, "abc");
        }, AssertionFailedException::class, "Test 5 failed. abc");
        $this->assertThrowsException(function () {
            $this->assertSame("abc", "def");
        }, AssertionFailedException::class, "Test 7 failed. The value is not 'abc' but 'def'.");
        $this->assertThrowsException(function () {
            $this->assertNotSame("abc", "abc");
        }, AssertionFailedException::class, "Test 9 failed. The value is 'abc'.");
        $this->assertThrowsException(function () {
            $this->assertGreaterThan(2, 1);
        }, AssertionFailedException::class, "Test 11 failed. 1 is not greater than 2.");
        $this->assertThrowsException(function () {
            $this->assertLessThan(1, 2);
        }, AssertionFailedException::class, "Test 13 failed. 2 is not less than 1.");
        $this->assertThrowsException(function () {
            $this->assertTrue(false);
        }, AssertionFailedException::class, "Test 15 failed. The value is not true.");
        $this->assertThrowsException(function () {
            $this->assertTruthy(false);
        }, AssertionFailedException::class, "Test 17 failed. The expression is not true.");
        $this->assertThrowsException(function () {
            $this->assertFalse(true);
        }, AssertionFailedException::class, "Test 19 failed. The value is not false.");
        $this->assertThrowsException(function () {
            $this->assertFalsey(true);
        }, AssertionFailedException::class, "Test 21 failed. The expression is not false.");
        $this->assertThrowsException(function () {
            $this->assertNull("abc");
        }, AssertionFailedException::class, "Test 23 failed. The value is not null.");
        $this->assertThrowsException(function () {
            $this->assertNotNull(null);
        }, AssertionFailedException::class, "Test 25 failed. The value is null.");
        $this->assertThrowsException(function () {
            $this->assertContains("abc", "def");
        }, AssertionFailedException::class, "Test 27 failed. abc is not in the variable.");
        $this->assertThrowsException(function () {
            $this->assertContains("abc", [1, 2, ]);
        }, AssertionFailedException::class, "Test 29 failed. 'abc' is not in the variable.");
        $this->assertThrowsException(function () {
            $this->assertContains(["abc", ], "abc");
        }, AssertionFailedException::class, "Test 31 failed. array (\n  0 => 'abc',\n) is not in the variable.");
        $this->assertThrowsException(function () {
            $this->assertNotContains("abc", "abc");
        }, AssertionFailedException::class, "Test 33 failed. abc is in the variable.");
        $this->assertThrowsException(function () {
            $this->assertNotContains("abc", ["abc", "def", ]);
        }, AssertionFailedException::class, "Test 35 failed. 'abc' is in the variable.");
        $this->assertThrowsException(function () {
            $this->assertNotContains(["abc", "def", ], "abc");
        }, AssertionFailedException::class,
            "Test 37 failed. array (\n  0 => 'abc',\n  1 => 'def',\n) is not in the variable.");
        $this->assertThrowsException(function () {
            $this->assertCount(1, ["abc", "def", ]);
        }, AssertionFailedException::class, "Test 39 failed. Count of the variable is not 1 but 2.");
        $this->assertThrowsException(function () {
            $this->assertNotCount(2, ["abc", "def", ]);
        }, AssertionFailedException::class, "Test 41 failed. Count of the variable is 2.");
        $this->assertThrowsException(function () {
            $this->assertType("string", ["abc", "def", ]);
        }, AssertionFailedException::class, "Test 43 failed. The variable is of type array not string.");
        $this->assertThrowsException(
            function () {
                $this->assertType(static::class, new stdClass());
            },
            AssertionFailedException::class,
            "Test 45 failed. The variable is instance of stdClass not " . static::class . "."
        );
        $this->assertThrowsException(function () {
            $this->assertThrowsException(function () {
            }, \Exception::class);
        }, AssertionFailedException::class, "Test 47 failed. The code does not throw any exception.");
        $this->assertThrowsException(function () {
            $this->assertThrowsException(function () {
                throw new \RuntimeException();
            }, \LogicException::class);
        }, AssertionFailedException::class,
            "Test 49 failed. The code does not throw LogicException but RuntimeException.");
        $this->assertThrowsException(function () {
            $this->assertThrowsException(function () {
                throw new \RuntimeException("def");
            }, \RuntimeException::class, "abc");
        }, AssertionFailedException::class,
            "Test 51 failed. The code does not throw an exception with message 'abc' but 'def'.");
        $this->assertThrowsException(function () {
            $this->assertThrowsException(function () {
                throw new \RuntimeException("abc", 2);
            }, \RuntimeException::class, "abc", 1);
        }, AssertionFailedException::class, "Test 53 failed. The code does not throw an exception with code 1 but 2.");
        $this->assertThrowsException(function () {
            $this->assertNoException(function () {
                throw new \RuntimeException();
            });
        }, AssertionFailedException::class,
            "Test 55 failed. No exception was expected but RuntimeException was thrown.");
        $this->assertThrowsException(function () {
            $this->assertMatchesRegExp('/abc/', "def");
        }, AssertionFailedException::class, "Test 57 failed. The string does not match regular expression.");
        $this->assertThrowsException(function () {
            $this->assertArrayOfClass(
                \stdClass::class,
                [new stdClass(), new DummyEngine(), "abc", ]
            );
        }, AssertionFailedException::class, "Test 59 failed. The array does not contain only instances of stdClass.");
        $this->assertThrowsException(function () {
            $this->assertMatchesFile(__DIR__ . "/non_existing.txt", "");
        }, AssertionFailedException::class,
            "Test 61 failed. File " . __DIR__ . "/non_existing.txt could not be loaded.");
        $this->assertThrowsException(function () {
            $this->assertMatchesFile(__DIR__ . "/test.txt", "");
        }, AssertionFailedException::class, "Test 63 failed. The value is not 'abc\n' but ''.");
        $this->assertThrowsException(function () {
            $this->assertArrayOfClass(
                \stdClass::class,
                []
            );
        }, AssertionFailedException::class, "Test 65 failed. The array is empty.");
        $this->assertThrowsException(function () {
            $this->assertTriggersDeprecation(function () {
            });
        }, AssertionFailedException::class, "Test 67 failed. Expected a deprecation but none was triggered.");
        $this->assertThrowsException(function () {
            $this->assertTriggersDeprecation(function () {
                trigger_error("test", E_USER_DEPRECATED);
            }, "abc");
        }, AssertionFailedException::class, "Test 69 failed. Expected deprecation 'abc' but 'test' was triggered.");
        $this->assertThrowsException(function () {
            $this->assertTriggersNoDeprecation(function () {
                trigger_error("test", E_USER_DEPRECATED);
            });
        }, AssertionFailedException::class, "Test 71 failed. Expected no deprecation but 'test' was triggered.");
        $this->assertThrowsException(function () {
            $this->assertArrayHasKey("test", ["abc" => 1, "def" => 2, ]);
        }, AssertionFailedException::class, "Test 73 failed. The array does not contain key 'test'.");
        $this->assertThrowsException(function () {
            $this->assertArrayHasKey(5, [0, 5, 10, ]);
        }, AssertionFailedException::class, "Test 75 failed. The array does not contain key 5.");
        $this->assertThrowsException(function () {
            $this->assertArrayHasKey("test", new ArrayObject());
        }, AssertionFailedException::class, "Test 77 failed. The array does not contain key 'test'.");
        $this->assertThrowsException(function () {
            $this->assertArrayNotHasKey("abc", ["abc" => 1, "def" => 2, ]);
        }, AssertionFailedException::class, "Test 79 failed. The array contains key 'abc'.");
        $this->assertThrowsException(function () {
            $this->assertArrayNotHasKey(1, [0, 5, 10, ]);
        }, AssertionFailedException::class, "Test 81 failed. The array contains key 1.");
        $this->assertThrowsException(function () {
            $arrayObject = new ArrayObject();
            $arrayObject->offsetSet("test", "abc");
            $this->assertArrayNotHasKey("test", $arrayObject);
        }, AssertionFailedException::class, "Test 83 failed. The array contains key 'test'.");
        $this->assertThrowsException(function () {
            $this->assertSameSize([], [0, ]);
        }, AssertionFailedException::class, "Test 85 failed. Actual count is 1 not 0.");
        $this->assertThrowsException(function () {
            $arrayObject = new ArrayObject();
            $arrayObject->offsetSet("test", "abc");
            $this->assertSameSize($arrayObject, []);
        }, AssertionFailedException::class, "Test 87 failed. Actual count is 0 not 1.");
        $this->assertThrowsException(function () {
            $this->assertSameSize(new ArrayObject(), [0, ]);
        }, AssertionFailedException::class, "Test 89 failed. Actual count is 1 not 0.");
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

    public function testShowValue(): void
    {
        $text = "abc";
        $this->assertSame("'$text'", $this->showValue($text));
        $this->assertSame("array (\n)", $this->showValue([]));
        $this->assertSame("array (\n  0 => 1,\n  1 => 2,\n  2 => 3,\n)", $this->showValue([1, 2, 3, ]));
        $this->assertSame("true", $this->showValue(true));
        $this->assertSame("1.2", $this->showValue(1.2));
        $this->assertSame("10", $this->showValue(10));
    }

    #[\Deprecated("test")]
    private function deprecatedMethod(): void
    {
    }
}
