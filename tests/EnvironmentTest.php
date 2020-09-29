<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suite for class Environment
 *
 * @author Jakub Konečný
 */
final class EnvironmentTest extends TestCase {
  /**
   * Tests for Environment
   *
   */
  public function testEnvironment(): void {
    $this->assertTrue(Environment::isSetUp());
    $this->assertSame(1, Environment::getCounter());
    Environment::incCounter();
    $this->assertSame(3, Environment::getCounter());
    Environment::resetCounter();
    $this->assertSame(0, Environment::getCounter());
    $this->assertFalse($this->shouldFail);
  }
}
?>