<?php
namespace MyTester;

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
  function testParams(array $params, $text) {
    Assert::same("abc", $params[0]);
    Assert::same("def", $text);
  }
}
?>