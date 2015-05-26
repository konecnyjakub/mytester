<?php
namespace MyTester\Tests;

require "../src/bootstrap.php";

use MyTester as MT;
use MyTester\Assert;

/**
 * Test suite for class Assert
 *
 * @author Jakub Konečný
 */
class AssertTest extends MT\TestCase {
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
}

MT\Environment::setup();

$suit = new AssertTest();
$suit->run();
?>