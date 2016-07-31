<?php
namespace MyTester\Tests;

use MyTester\Environment,
    MyTester\Runner,
    MyTester\Job;

/**
 * Tests for class Runner
 *
 * @author Jakub Konečný
 * @deprecated since version 1.0
 */
class TestJobsRunner {
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

/**
 * Test suite for class Runner
 *
 * @author Jakub Konečný
 * @deprecated since version 1.0
 * @testSuit TestRunner
 */
class RunnerTest extends \MyTester\TestCase {
  /**
   * Runs the test suit
   * 
   * @return void
   * @deprecated since version 1.0
   */
  function run() {
    $suitName = $this->getSuitName();
    $test = new TestJobsRunner();
    $runner = new Runner($suitName);
    $runner->addJob("Test Job", [$test, "test"]);
    $runner->addJob("Test Skipped Job", [$test, "test"], NULL, true);
    $runner->addJob(new Job("Test Job 2", [$test, "testTwo"]));
    $output = $runner->run();
    if(Environment::getOutput() == "screen") {
      echo $output;
    } else {
      $time = date("o-m-d-h-i-s");
      $filename = "../$suitName-$time.log";
      Environment::printLine("Trying to create file $filename ...", true);
      if(file_put_contents($filename, $output)) {
        Environment::printLine("Successfuly created.", true);
      } else {
        Environment::printLine("An error occurred.", true);
      }
    }
  }
}

$suit = new RunnerTest();
$suit->run();
?>