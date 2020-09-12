<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite for class Assert
 *
 * @author Jakub Konečný
 */
final class AssertTest extends TestCase {
  public function shutDown(): void {
    $this->assertFalse(Environment::getShouldFail());
  }
  
  /**
   * Test assertion functions
   */
  public function testAssertion(): void {
    $this->assertSame("abc", "abc");
    $this->assertNotSame("abc", "def");
    $this->assertTrue(1);
    $this->assertFalse(0);
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
    $this->assertType("object", new \stdClass());
    $this->assertType("scalar", 42);
  }
  
  /**
   * Test assertion function, all should produce error
   *
   * @fail
   */
  public function testAssertionFails(): void {
    $actual = "abc";
    $this->assertTrue(0);
    $this->assertFalse(1);
    $this->assertSame("def", $actual);
    $this->assertContains("def", $actual);
    $this->assertNotContains("abc", $actual);
    $this->assertCount(1, $actual);
    $this->assertCount(0, [$actual]);
    $this->assertNotCount(1, $actual);
    $this->assertNotCount(1, [$actual]);
    $this->assertSame("abc", "def");
    $this->assertType("array", $actual);
    $this->assertType("abc", $this);
    $this->assertType("string", true);
    $this->assertType("bool", $actual);
    $this->assertType("int", "42");
    $this->assertType("null", $actual);
    $this->assertType("object", true);
    $this->assertType("scalar", new \stdClass());
  }
  
  /**
   * Test custom assertions and custom messages
   */
  public function testCustomAssertion(): void {
    $this->assert("5 > 2", "5 is not greater that 2.");
    $this->assert("5 >= 2", "5 is not greater or equal to 2.");
    $this->assert("2 < 5", "2 is not lesser than 5.");
    $this->assert("2 <= 5", "2 is not lesser or equal to 5.");
    $this->assert("abc != def", "abc is def.");
  }
}
?>