<?php
namespace MyTester;

/**
 * Test suite for class Runner
 *
 * @author Jakub Konečný
 */
class RunnerTest {
  /**
   * Test for Environment::testResult()
   * 
   * @return void
   */
  function test() {
    Environment::testResult("Test", false);
  }
}

$test = new RunnerTest();
$runner = new Runner("Test Runner");
$runner->addJob("Test Job", array($test, "test"));

echo $runner->run();
?>