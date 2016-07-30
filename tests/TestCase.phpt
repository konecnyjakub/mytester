<?php
namespace MyTester\Tests;

use MyTester as MT;
use MyTester\Assert;

/**
 * Test suite for class TestCase
 * 
 * @testSuit TestCase
 * @author Jakub Konečný
 * @property-read bool|int $one
 */
class TestCaseTest extends MT\TestCase {
  private $one = false;
  
  function getOne() {
    return $this->one;
  }
  
  function setUp() {
    $this->one = 1;
  }
  
  function tearDown() {
    $this->one = false;
  }
  
  /**
   * Test parameters
   * 
   * @param string $text
   * @return void
   * @data(abc, adef)   
   */
  function testParams($text) {
    Assert::type("string", $text);
    Assert::contains("a", $text);
  }
  
  /**
   * Test custome test's name
   * 
   * @test Custom name
   * @return void
   */
  function testTestName() {
    Assert::same(1, $this->one);
  }
  
  /**
   * Test unconditional skipping
   * 
   * @test Skip
   * @skip
   * @return void
   */
  function testSkip() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on boolean
   * 
   * @test Boolean
   * @skip(true)
   * @return void
   */
  function testSkipBoolean() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on integer
   * 
   * @test Integer
   * @skip(1)
   * @return void
   */
  function testSkipInteger() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on float
   * 
   * @test Float
   * @skip(1.5)
   * @return void
   */
  function testSkipFloat() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on string
   * 
   * @test String
   * @skip(abc)
   * @return void
   */
  function testSkipString() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on PHP version
   * 
   * @test PHP version
   * @skip(php=8)
   * @return void
   */
  function testSkipPhpVersion() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on loaded extension
   * 
   * @test Extension
   * @skip(extension=abc)
   */
  function testSkipExtension() {
    Assert::true(1);
  }
}

$suit = new TestCaseTest();
$suit->run();

Assert::false($suit->one);
?>