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
    $this->assertType("string", Environment::getMode());
    $this->assertTrue(Environment::isSetUp());
    $this->assertSame(2, Environment::getCounter());
    Environment::incCounter();
    $this->assertSame(4, Environment::getCounter());
    Environment::resetCounter();
    $this->assertSame(0, Environment::getCounter());
    $this->assertType("bool", Environment::getShouldFail());
    $this->assertFalse(Environment::getShouldFail());
  }

  /**
   * Test skipping based on sapi
   *
   * @test CGI sapi
   * @skip(sapi=cgi-fcgi)
   */
  public function testCgiSapi(): void {
    $this->assertSame(Environment::MODE_HTTP, Environment::getMode());
  }

  /**
   * Test skipping based on sapi
   *
   * @test CLI sapi
   * @skip(sapi=cli)
   */
  public function testCliSapi(): void {
    $this->assertSame(Environment::MODE_CLI, Environment::getMode());
  }
}
?>