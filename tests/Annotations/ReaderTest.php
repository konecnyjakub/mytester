<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\ShouldFailChecker;
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
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertFalse((new Reader())->hasAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
  }

  public function testGetAnnotation(): void {
    $this->assertNull((new Reader())->getAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertSame("ReaderTest", $this->getAnnotationsReader()->hasAnnotation("testSuit", static::class));
    $this->assertNull((new Reader())->getAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
    $this->assertSame(1, $this->getAnnotationsReader()->getAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
  }

  /**
   * @fail(1)
   */
  private function method(): void {
  }
}
?>