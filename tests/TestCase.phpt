<?php
namespace MyTester\Tests;

use MyTester as MT;
use MyTester\Assert;

/**
 * Test suite for class TestCase
 * 
 * @testSuit TestCase
 * @author Jakub Konečný
 */
class TestCaseTest extends MT\TestCase {
  /**
   * Test parameters
   * 
   * @param string $text
   * @return void
   * @data(abc, adef)   
   */
  function testParams($text) {
    Assert::contains("a", $text);
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
  
  /**
   * Test skipping
   * 
   * @test Skipped test
   * @skip(php=8)
   * @return void
   */
  function testSkip() {
    Assert::true(1);
  }
}

$params = array("abc");
$text = "def";

$suit = new TestCaseTest();
$suit->run();
?>