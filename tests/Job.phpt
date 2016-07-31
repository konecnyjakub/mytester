<?php
namespace MyTester\Tests;

use MyTester\Assert,
    MyTester\Job,
    MyTester\Environment;

/**
 * Tests for class Job
 *
 * @author Jakub Konečný
 */
class TestJobs {
  /**
   * Test for Environment::testResult()
   * 
   * @return void
   */
  function test() {
    Environment::testResult("Test");
  }
  
  /**
   * Test params for job
   * 
   * @param array $params
   * @param string $text
   * @return void
   */
  function testParams($params, $text) {
    Assert::same("abc", $params[0]);
    Assert::same("def", $text);
  }
}

/**
 * Test suite for class Job
 *
 * @author Jakub Konečný
 * @testSuit JobTest
 */
class JobTest extends \MyTester\TestCase {
  protected function getJobs() {
    $test = new TestJobs();
    $job = new Job("Test Job", [$test, "test"]);
    $params = [
      ["abc"], "def"
    ];
    $job2 = new Job("Test Job with Params", [$test, "testParams"], $params);
    $job3 = new Job("Test Skipped Job", [$test, "test"], NULL, true);
    return [$job, $job2, $job3];
  }
}

$suit = new JobTest();
$suit->run();
?>