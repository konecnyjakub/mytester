<?php
namespace MyTester\Tests;

use MyTester as MT;
use MyTester\Assert;

/**
 * Test suite for class TestCase
 * 
 * @author Jakub Konečný
 */
class TestCaseTest extends MT\TestCase {
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
  
  /**
   * Test custome test's name
   * 
   * @test Custom name
   * @return void
   */
  function testTestName() {
    Assert::true(1);
  }
}

$params = array("abc");
$text = "def";

$suit = new TestCaseTest();
$suit->run();
?>