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
    Assert::contains("def", $actual);
    Assert::notContains("abc", $actual);
    Assert::count(1, $actual);
    Assert::same("abc", "def");
    Assert::type("array", $actual);
    Assert::type("abc", $this);
    Assert::type("string", true);
  }
  
  
  /**
   * Test custom assertions
   *
   * @return void      
   */     
  function testCustomAssertion() {
    Assert::tryAssertion("5 > 2");
    Assert::tryAssertion("5 >= 2");
    Assert::tryAssertion("2 < 5");
    Assert::tryAssertion("2 <= 5");
    Assert::tryAssertion("abc != def");
  }
  
  /**
   * Test parameters
   * 
   * @param array $params
   * @param string $text
   * @return void
   */
  function testParams($params, $text) {
    $actual = $params[0];
    Assert::same("abc", $actual);
    Assert::same("def", $text);
  }
}

$params = array("abc");
$text = "def";
$suit = new AssertTest();
$suit->run();
?>