<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Annotations\Attributes\Fail;
use MyTester\Annotations\Attributes\TestSuit;
use MyTester\ShouldFailChecker;
use MyTester\TestCase;

/**
 * Test suite for class Reader
 *
 * @testSuit Reader
 * @author Jakub Konečný
 */
#[TestSuit("Reader")]
final class ReaderTest extends TestCase {
  private function getAnnotationsReader(): Reader {
    static $annotationsReader = null;
    if($annotationsReader === null) {
      $annotationsReader = new Reader();
      $annotationsReader->registerEngine(new DummyEngine());
    }
    return $annotationsReader;
  }

  public function testHasAnnotation(): void {
    $this->assertFalse((new Reader())->hasAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertFalse((new Reader())->hasAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
  }

  public function testGetAnnotation(): void {
    $this->assertNull((new Reader())->getAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertSame("abc", $this->getAnnotationsReader()->getAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertNull((new Reader())->getAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
    $this->assertSame("abc", $this->getAnnotationsReader()->getAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
  }

  /**
   * @fail(1)
   */
  #[Fail(1)]
  private function method(): void {
  }
}
?>