<?php
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
  function testEnvironment() {
    Assert::type("string", Environment::getMode());
    Assert::true(Environment::isSetUp());
    Assert::same(2, Environment::getCounter());
    Environment::incCounter();
    Assert::same(4, Environment::getCounter());
    Environment::resetCounter();
    Assert::same(0, Environment::getCounter());
  }
  
  /**
   * Test skipping based on sapi
   * 
   * @return void
   * @test CGI sapi
   * @skip(sapi=cgi-fcgi)
   */
  function testCgiSapi() {
    Assert::same("http", Environment::getMode());
  }
  
  /**
   * Test skipping based on sapi
   * 
   * @return void
   * @test CLI sapi
   * @skip(sapi=cli)
   */
  function testCliSapi() {
    Assert::same("cli", Environment::getMode());
  }
}
?>