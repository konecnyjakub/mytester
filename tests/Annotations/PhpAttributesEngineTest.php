<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
use MyTester\SkipChecker;
use MyTester\TestCase;

/**
 * Test suite for class PhpAttributesEngine
 *
 * @author Jakub Konečný
 */
#[TestSuite("PhpAttributesEngine")]
final class PhpAttributesEngineTest extends TestCase
{
    private function getAnnotationsReader(): Reader
    {
        static $annotationsReader = null;
        if ($annotationsReader === null) {
            $annotationsReader = new Reader();
            $annotationsReader->registerEngine(new PhpAttributesEngine());
        }
        return $annotationsReader;
    }

    public function testHasAnnotation(): void
    {
        $this->assertFalse((new Reader())->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, static::class));
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, static::class));
        $this->assertFalse((new Reader())->hasAnnotation(SkipChecker::ANNOTATION_NAME, static::class, "method"));
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(SkipChecker::ANNOTATION_NAME, static::class, "method"));
    }

    public function testGetAnnotation(): void
    {
        $this->assertNull((new Reader())->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, static::class));
        $this->assertSame("NetteReflectionEngine", $this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, static::class));
        $this->assertNull((new Reader())->getAnnotation(SkipChecker::ANNOTATION_NAME, static::class, "method"));
        $this->assertSame(1, $this->getAnnotationsReader()->getAnnotation(SkipChecker::ANNOTATION_NAME, static::class, "method"));
    }

    #[Skip(1)]
    private function method(): void
    {
    }
}
