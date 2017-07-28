<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Tests for class Job
 *
 * @author Jakub Konečný
 */
class TestJobs {
  /**
   * Test for Environment::testResult()
   */
  public function test() {
    Environment::testResult("Test");
  }
  
  /**
   * Test params for job
   */
  public function testParams(array $params, string $text) {
    Assert::same("abc", $params[0]);
    Assert::same("def", $text);
  }
}
?>