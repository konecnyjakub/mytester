<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite for class Assert
 *
 * @author Jakub Konečný
 */
final class AssertTest extends TestCase {
  public function shutDown() {
    Assert::false(Environment::getShouldFail());
  }
  
  /**
   * Test assertion function
   */
  public function testAssertion() {
    Assert::same("abc", "abc");
    Assert::notSame("abc", "def");
    Assert::true(1);
    Assert::false(0);
    Assert::null(NULL);
    Assert::notNull("abc");
    $testArray = ["abc"];
    Assert::contains("abc", $testArray);
    Assert::notContains("def", $testArray);
    Assert::count(1, $testArray);
    Assert::notCount(0, $testArray);
    Assert::type("array", $testArray);
    Assert::type(__CLASS__, $this);
    Assert::type("string", "abc");
    Assert::type("bool", true);
    Assert::type("int", 42);
    Assert::type("null", NULL);
    Assert::type("object", new \stdClass);
    Assert::type("scalar", 42);
  }
  
  /**
   * Test assertion function, all should produce error
   *
   * @fail
   */
  public function testAssertionFails() {
    $actual = "abc";
    Assert::true(0);
    Assert::false(1);
    Assert::same("def", $actual);
    Assert::contains("def", $actual);
    Assert::notContains("abc", $actual);
    Assert::count(1, $actual);
    Assert::count(0, [$actual]);
    Assert::notCount(1, $actual);
    Assert::notCount(1, [$actual]);
    Assert::same("abc", "def");
    Assert::type("array", $actual);
    Assert::type("abc", $this);
    Assert::type("string", true);
    Assert::type("bool", $actual);
    Assert::type("int", "42");
    Assert::type("null", $actual);
    Assert::type("object", true);
    Assert::type("scalar", new \stdClass);
  }
  
  /**
   * Test custom assertions and custom messages
   */
  public function testCustomAssertion() {
    Assert::tryAssertion("5 > 2", "5 is greater than 2.", "5 is not greater that 2.");
    Assert::tryAssertion("5 >= 2", "5 is greater or equal to 2.", "5 is not greater or equal to 2.");
    Assert::tryAssertion("2 < 5", "2 is lesser than 5.", "2 is not lesser than 5.");
    Assert::tryAssertion("2 <= 5", "2 is lesser or equal to 5.", "2 is not lesser or equal to 5.");
    Assert::tryAssertion("abc != def", "abc is not def.", "abc is def.");
  }
}
?>