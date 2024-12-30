<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
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
        $this->assertFalse((new Reader())->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertFalse(
            (new Reader())->hasAnnotation("skip", self::class, "method")
        );
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(
            "skip",
            self::class,
            "method"
        ));
    }

    public function testGetAnnotation(): void
    {
        $this->assertNull((new Reader())->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertSame("abc", $this->getAnnotationsReader()->getAnnotation(
            TestCase::ANNOTATION_TEST_SUITE,
            self::class
        ));
        $this->assertNull(
            (new Reader())->getAnnotation("skip", self::class, "method")
        );
        $this->assertSame("abc", $this->getAnnotationsReader()->getAnnotation(
            "skip",
            self::class,
            "method"
        ));
    }

    public function testGetAnnotationMulti(): void
    {
        $this->assertSame([], (new Reader())->getAnnotationMulti(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertSame(
            ["abc", "def", ],
            $this->getAnnotationsReader()->getAnnotationMulti(TestCase::ANNOTATION_TEST_SUITE, self::class)
        );
        $this->assertSame(
            [],
            (new Reader())->getAnnotationMulti(TestCase::ANNOTATION_TEST_SUITE, self::class, "method")
        );
        $this->assertSame(
            ["abc", "def", ],
            $this->getAnnotationsReader()->getAnnotationMulti(TestCase::ANNOTATION_TEST_SUITE, self::class, "method")
        );
    }

    #[Skip()]
    private function method(): void
    {
    }
}
