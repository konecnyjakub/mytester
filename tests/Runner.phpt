<?php
namespace MyTester;

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
}

$test = new RunnerTest();
$runner = new Runner("Test Runner");
$runner->addJob("Test Job", [$test, "test"]);
$runner->addJob("Test Skipped Job", [$test, "test"], NULL, true);

echo $runner->run();
?>