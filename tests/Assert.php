<?php
namespace MyTester\Tests;

require_once "../src/bootstrap.php";

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
  }
}

MT\Environment::setup();

$suit = new AssertTest();
$suit->run();
?>