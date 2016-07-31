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
    Assert::same("cli", MT\Environment::getMode());
    Assert::true(MT\Environment::isSetUp());
    Assert::same(2, MT\Environment::getCounter());
    MT\Environment::incCounter();
    Assert::same(4, MT\Environment::getCounter());
    MT\Environment::resetCounter();
    Assert::same(0, MT\Environment::getCounter());
  }
}
?>