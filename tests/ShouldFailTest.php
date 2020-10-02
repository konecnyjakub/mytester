<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Attributes\Fail;
use MyTester\Annotations\Attributes\TestSuite;

/**
 * Test suite for class ShouldFail
 *
 * @testSuite ShouldFail
 * @author Jakub Konečný
 */
#[TestSuite("ShouldFail")]
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
  #[Fail()]
  private function shouldFail(): void {
  }
}
?>