<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\TestCase;

/**
 * Test suite for class Reader
 *
 * @testSuit ReaderTest
 * @author Jakub Konečný
 */
final class ReaderTest extends TestCase {
  private function getAnnotationsReader(): Reader {
    return $this->annotationsReader;
  }

  public function testHasAnnotation(): void {
    $this->assertFalse((new Reader())->hasAnnotation("testSuit", static::class));
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation("testSuit", static::class));
    $this->assertFalse((new Reader())->hasAnnotation("fail", static::class, "method"));
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation("fail", static::class, "method"));
  }

  public function testGetAnnotation(): void {
    $this->assertNull((new Reader())->getAnnotation("testSuit", static::class));
    $this->assertSame("ReaderTest", $this->getAnnotationsReader()->hasAnnotation("testSuit", static::class));
    $this->assertNull((new Reader())->getAnnotation("fail", static::class, "method"));
    $this->assertSame(1, $this->getAnnotationsReader()->getAnnotation("fail", static::class, "method"));
  }

  /**
   * @fail(1)
   */
  private function method(): void {
  }
}
?>