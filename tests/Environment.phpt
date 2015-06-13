<?php
namespace MyTester\Tests;

use MyTester as MT;
use MyTester\Assert;

class EnvironmentTest extends MT\TestCase {
  function testEnvironment() {
    Assert::same("cli", MT\Environment::getMode());
    Assert::true(MT\Environment::isSetUp());
    Assert::same(2, MT\Environment::$taskCount);
    MT\Environment::incCounter();
    Assert::same(4, MT\Environment::$taskCount);
    MT\Environment::resetCounter();
    Assert::same(0, MT\Environment::$taskCount);
  }
}

$suit = new EnvironmentTest();
$suit->run();
?>