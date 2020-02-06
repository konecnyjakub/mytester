<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Tests for class Job
 *
 * @author Jakub Konečný
 */
final class TestJobs {
  /**
   * Test for Environment::testResult()
   */
  public function test(): void {
    Environment::testResult("Test");
  }
  
  /**
   * Test params for job
   */
  public function testParams(array $params, string $text): void {
    Assert::same("abc", $params[0]);
    Assert::same("def", $text);
  }
}
?>