<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite for class TestCase
 * 
 * @testSuit TestCase
 * @author Jakub Konečný
 * @property-read bool|int $one
 */
final class TestCaseTest extends TestCase {
  /** @var bool|int */
  private $one = false;

  /**
   * @return bool|int
   */
  public function getOne() {
    return $this->one;
  }
  
  public function setUp(): void {
    $this->one = 1;
  }
  
  public function tearDown(): void {
    $this->one = false;
  }
  
  public function shutDown(): void {
    Assert::false($this->one);
    Assert::same("", Environment::$currentJob);
  }
  
  /**
   * Test parameters
   *
   * @data(abc, adef)   
   */
  public function testParams(string $text): void {
    Assert::type("string", $text);
    Assert::contains("a", $text);
  }
  
  /**
   * Test custom test's name
   * 
   * @test Custom name
   */
  public function testTestName(): void {
    Assert::same("Custom name", Environment::$currentJob);
    Assert::same(1, $this->one);
  }
  
  /**
   * Test unconditional skipping
   * 
   * @test Skip
   * @skip
   */
  public function testSkip(): void {
    Assert::true(0);
  }
  
  /**
   * Test skipping based on boolean
   * 
   * @test Boolean
   * @skip(true)
   */
  public function testSkipBoolean(): void {
    Assert::true(0);
  }
  
  /**
   * Test skipping based on integer
   * 
   * @test Integer
   * @skip(1)
   */
  public function testSkipInteger(): void {
    Assert::true(0);
  }
  
  /**
   * Test skipping based on float
   * 
   * @test Float
   * @skip(1.5)
   */
  public function testSkipFloat(): void {
    Assert::true(0);
  }
  
  /**
   * Test skipping based on string
   * 
   * @test String
   * @skip(abc)
   */
  public function testSkipString(): void {
    Assert::true(0);
  }
  
  /**
   * Test skipping based on PHP version
   * 
   * @test PHP version
   * @skip(php=666)
   */
  public function testSkipPhpVersion(): void {
    Assert::true(0);
  }
  
  /**
   * Test skipping based on loaded extension
   * 
   * @test Extension
   * @skip(extension=abc)
   */
  public function testSkipExtension(): void {
    Assert::true(0);
  }
}
?>