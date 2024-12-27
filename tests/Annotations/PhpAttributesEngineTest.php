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
        $annotationsReader = new Reader();
        $annotationsReader->registerEngine(new PhpAttributesEngine());
        return $annotationsReader;
    }

    public function testHasAnnotation(): void
    {
        $this->assertFalse((new Reader())->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertFalse((new Reader())->hasAnnotation("skip", self::class, "method"));
        $this->assertTrue($this->getAnnotationsReader()->hasAnnotation(
            SkipChecker::ANNOTATION_NAME,
            self::class,
            "method"
        ));
    }

    public function testGetAnnotation(): void
    {
        $this->assertNull((new Reader())->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertSame("NetteReflectionEngine", $this->getAnnotationsReader()->hasAnnotation(
            TestCase::ANNOTATION_TEST_SUITE,
            self::class
        ));
        $this->assertNull((new Reader())->getAnnotation(SkipChecker::ANNOTATION_NAME, self::class, "method"));
        $this->assertSame("", $this->getAnnotationsReader()->getAnnotation(
            "skip",
            self::class,
            "method"
        ));
    }

    #[Skip()]
    private function method(): void
    {
    }
}
