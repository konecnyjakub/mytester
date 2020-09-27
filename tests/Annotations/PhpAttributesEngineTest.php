<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Annotations\Attributes\Fail;
use MyTester\Annotations\Attributes\Skip;
use MyTester\Annotations\Attributes\TestSuit;
use MyTester\ShouldFailChecker;
use MyTester\TestCase;

/**
 * Test suite for class PhpAttributesEngine
 *
 * @author Jakub Konečný
 */
#[TestSuit("PhpAttributesEngine")]
final class PhpAttributesEngineTest extends TestCase {
  private function getAnnotationsReader(): Reader {
    static $annotationsReader = null;
    if($annotationsReader === null) {
      $annotationsReader = new Reader();
      $annotationsReader->registerEngine(new PhpAttributesEngine());
    }
    return $annotationsReader;
  }

  /**
   * @skip(php=7.5)
   */
  #[Skip(["php" => "7.5"])]
  public function testHasAnnotation(): void {
    $this->assertFalse((new Reader())->hasAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertFalse((new Reader())->hasAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
    $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
  }

  /**
   * @skip(php=7.5)
   */
  #[Skip(["php" => "7.5"])]
  public function testGetAnnotation(): void {
    $this->assertNull((new Reader())->getAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertSame("NetteReflectionEngine", $this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUIT, static::class));
    $this->assertNull((new Reader())->getAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
    $this->assertSame(1, $this->getAnnotationsReader()->getAnnotation(ShouldFailChecker::ANNOTATION_NAME, static::class, "method"));
  }

  #[Fail(1)]
  private function method(): void {
  }
}
?>