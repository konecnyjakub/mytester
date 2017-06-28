<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite for class Environment
 *
 * @author Jakub Konečný
 */
class EnvironmentTest extends TestCase {
  /**
   * Tests for Environment
   * 
   * @return void
   */
  public function testEnvironment() {
    Assert::type("string", Environment::getMode());
    Assert::true(Environment::isSetUp());
    Assert::same(2, Environment::getCounter());
    Environment::incCounter();
    Assert::same(4, Environment::getCounter());
    Environment::resetCounter();
    Assert::same(0, Environment::getCounter());
    Assert::type("bool", Environment::getShouldFail());
    Assert::false(Environment::getShouldFail());
  }
  
  /**
   * Test skipping based on sapi
   * 
   * @return void
   * @test CGI sapi
   * @skip(sapi=cgi-fcgi)
   */
  public function testCgiSapi() {
    Assert::same("http", Environment::getMode());
  }
  
  /**
   * Test skipping based on sapi
   * 
   * @return void
   * @test CLI sapi
   * @skip(sapi=cli)
   */
  public function testCliSapi() {
    Assert::same("cli", Environment::getMode());
  }
}
?>