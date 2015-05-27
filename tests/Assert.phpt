<?php
namespace MyTester\Tests;

use MyTester as MT;
use MyTester\Assert;

/**
 * Test suite for class Assert
 *
 * @author Jakub Konečný
 */
class AssertTest extends MT\TestCase {
  /**
   * Test assertion function
   * 
   * @return void
   */
  function testAssertion() {
    Assert::same("abc", "abc");
    Assert::notSame("abc", "def");
    Assert::true(1);
    Assert::false(0);
    Assert::null(NULL);
    Assert::notNull("abc");
    $testArray = array("abc");
    Assert::contains("abc", $testArray);
    Assert::notContains("def", $testArray);
    Assert::count(1, $testArray);
    Assert::type("array", $testArray);
    Assert::type(__CLASS__, $this);
    Assert::type("string", "abc");
  }
  
  /**
   * Test assertion function, all should produce error
   * 
   * @return void
   */
  function testAssertionFails() {
    $actual = "abc";
    Assert::contains("abc", $actual);
    Assert::notContains("abc", $actual);
    Assert::count(1, $actual);
    Assert::same("abc", "def");
    Assert::type("array", $actual);
    Assert::type("abc", $this);
    Assert::type("string", true);
  }
}

$suit = new AssertTest();
$suit->run();
?>