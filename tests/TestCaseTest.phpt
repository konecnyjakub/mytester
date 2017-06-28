<?php
namespace MyTester;

/**
 * Test suite for class TestCase
 * 
 * @testSuit TestCase
 * @author Jakub Konečný
 * @property-read bool|int $one
 */
class TestCaseTest extends TestCase {
  private $one = false;
  
  public function getOne() {
    return $this->one;
  }
  
  public function setUp() {
    $this->one = 1;
  }
  
  public function tearDown() {
    $this->one = false;
  }
  
  public function shutDown() {
    Assert::false($this->one);
    Assert::same("", Environment::$currentJob);
  }
  
  /**
   * Test parameters
   * 
   * @param string $text
   * @return void
   * @data(abc, adef)   
   */
  public function testParams(string $text) {
    Assert::type("string", $text);
    Assert::contains("a", $text);
  }
  
  /**
   * Test custom test's name
   * 
   * @test Custom name
   * @return void
   */
  public function testTestName() {
    Assert::same("Custom name", Environment::$currentJob);
    Assert::same(1, $this->one);
  }
  
  /**
   * Test unconditional skipping
   * 
   * @test Skip
   * @skip
   * @return void
   */
  public function testSkip() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on boolean
   * 
   * @test Boolean
   * @skip(true)
   * @return void
   */
  public function testSkipBoolean() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on integer
   * 
   * @test Integer
   * @skip(1)
   * @return void
   */
  public function testSkipInteger() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on float
   * 
   * @test Float
   * @skip(1.5)
   * @return void
   */
  public function testSkipFloat() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on string
   * 
   * @test String
   * @skip(abc)
   * @return void
   */
  public function testSkipString() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on PHP version
   * 
   * @test PHP version
   * @skip(php=8)
   * @return void
   */
  public function testSkipPhpVersion() {
    Assert::true(1);
  }
  
  /**
   * Test skipping based on loaded extension
   * 
   * @test Extension
   * @skip(extension=abc)
   */
  public function testSkipExtension() {
    Assert::true(1);
  }
}
?>