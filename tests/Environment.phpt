<?php
namespace MyTester\Tests;

use MyTester as MT;
use MyTester\Assert;

/**
 * Test suite for class Environment
 *
 * @author Jakub Konečný
 */
class EnvironmentTest extends MT\TestCase {
  /**
   * Tests for Environment
   * 
   * @return void
   */
  function testEnvironment() {
    Assert::type("string", MT\Environment::getMode());
    Assert::true(MT\Environment::isSetUp());
    Assert::same(2, MT\Environment::getCounter());
    MT\Environment::incCounter();
    Assert::same(4, MT\Environment::getCounter());
    MT\Environment::resetCounter();
    Assert::same(0, MT\Environment::getCounter());
    Assert::type("bool", MT\Environment::getShouldFail());
    Assert::false(MT\Environment::getShouldFail());
  }
  
  /**
   * Test skipping based on sapi
   * 
   * @return void
   * @test CGI sapi
   * @skip(sapi=cgi-fcgi)
   */
  function testCgiSapi() {
    Assert::same("http", MT\Environment::getMode());
  }
  
  /**
   * Test skipping based on sapi
   * 
   * @return void
   * @test CLI sapi
   * @skip(sapi=cli)
   */
  function testCliSapi() {
    Assert::same("cli", MT\Environment::getMode());
  }
}
?>