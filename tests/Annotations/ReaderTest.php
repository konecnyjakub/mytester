<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
use MyTester\SkipChecker;
use MyTester\TestCase;

/**
 * Test suite for class Reader
 *
 * @author Jakub Konečný
 */
#[TestSuite("Reader")]
final class ReaderTest extends TestCase
{
    private function getAnnotationsReader(): Reader
    {
        $annotationsReader = new Reader();
        $annotationsReader->registerEngine(new DummyEngine());
        return $annotationsReader;
    }

    public function testHasAnnotation(): void
    {
        $this->assertFalse((new Reader())->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, static::class));
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, static::class));
        $this->assertFalse((new Reader())->hasAnnotation(SkipChecker::ANNOTATION_NAME, static::class, "method"));
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(
            SkipChecker::ANNOTATION_NAME,
            static::class,
            "method"
        ));
    }

    public function testGetAnnotation(): void
    {
        $this->assertNull((new Reader())->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, static::class));
        $this->assertSame("abc", $this->getAnnotationsReader()->getAnnotation(
            TestCase::ANNOTATION_TEST_SUITE,
            static::class
        ));
        $this->assertNull((new Reader())->getAnnotation(SkipChecker::ANNOTATION_NAME, static::class, "method"));
        $this->assertSame("abc", $this->getAnnotationsReader()->getAnnotation(
            SkipChecker::ANNOTATION_NAME,
            static::class,
            "method"
        ));
    }

    #[Skip()]
    private function method(): void
    {
    }
}
