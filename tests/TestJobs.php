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
    $this->assertSame("abc", $params[0]);
    $this->assertSame("def", $text);
  }

  /**
   * @param mixed $expected
   * @param mixed $actual
   */
  private function assertSame($expected, $actual): void {
    $success = ($expected == $actual);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = "The value is not $expected but $actual.";
    }
    Environment::testResult($message ?? "", $success);
  }
}
?>