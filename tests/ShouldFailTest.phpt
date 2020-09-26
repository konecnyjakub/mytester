<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite for class ShouldFail
 *
 * @testSuit ShouldFailTest
 * @author Jakub Konečný
 */
final class ShouldFailTest extends TestCase {
  private function getShouldFailChecker(): ShouldFailChecker {
    return $this->shouldFailChecker;
  }

  public function testShouldFail(): void {
    $this->assertFalse($this->getShouldFailChecker()->shouldFail(static::class, "shouldFailFalse"));
    $this->assertTrue($this->getShouldFailChecker()->shouldFail(static::class, "shouldFail"));
  }

  private function shouldFailFalse(): void {
  }

  /**
   * @fail
   */
  private function shouldFail(): void {
  }
}
?>