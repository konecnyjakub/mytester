<?php
namespace MyTester\Tests;

use MyTester\Environment,
    MyTester\Runner,
    MyTester\Job;

/**
 * Test suite for class Runner
 *
 * @author Jakub Konečný
 * @deprecated since version 1.0
 */
class RunnerTest {
  /**
   * Test for Environment::testResult()
   * 
   * @return void
   * @deprecated since version 1.0
   */
  function test() {
    Environment::testResult("Test", false);
  }
  
  /**
   * Test for Environment::testResult()
   * 
   * @return void
   * @deprecated since version 1.0
   */
  function testTwo() {
    Environment::testResult("Test");
  }
}

$test = new RunnerTest();
$runner = new Runner("Test Runner");
$runner->addJob("Test Job", [$test, "test"]);
$runner->addJob("Test Skipped Job", [$test, "test"], NULL, true);
$runner->addJob(new Job("Test Job 2", [$test, "testTwo"]));

echo $runner->run();
?>