<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Attributes\Data;
use MyTester\Attributes\DataProviderExternal;
use MyTester\Attributes\Group;
use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
use MyTester\AnnotationsDataProvider;
use MyTester\ExternalDataProvider;
use MyTester\TestCase;

/**
 * Test suite for class PhpAttributesEngine
 *
 * @author Jakub Konečný
 */
#[TestSuite("PhpAttributesEngine")]
#[Group("annotations")]
final class PhpAttributesEngineTest extends TestCase
{
    public function testHasAnnotation(): void
    {
        $engine = new PhpAttributesEngine();
        $this->assertTrue($engine->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertFalse($engine->hasAnnotation("skip", self::class));
        $this->assertTrue($engine->hasAnnotation("skip", self::class, "method"));
        $this->assertFalse($engine->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class, "method"));
    }

    public function testGetAnnotation(): void
    {
        $engine = new PhpAttributesEngine();
        $this->assertNull($engine->getAnnotation("skip", self::class));
        $this->assertSame("PhpAttributesEngine", $engine->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertNull($engine->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class, "method"));
        $this->assertSame("", $engine->getAnnotation("skip", self::class, "method"));
        $this->assertSame(
            ExternalDataProvider::class . "::dataProviderArray",
            $engine->getAnnotation(
                AnnotationsDataProvider::ANNOTATION_EXTERNAL_NAME,
                self::class,
                "dataProviderExternal"
            )
        );
    }

    public function testGetAnnotationMulti(): void
    {
        $engine = new PhpAttributesEngine();
        $this->assertSame(
            [],
            $engine->getAnnotationMulti(AnnotationsDataProvider::ANNOTATION_SIMPLE_NAME, self::class)
        );
        $this->assertSame(
            [
                ["abc", "def", ],
                ["ghi", "jkl", ],
            ],
            $engine->getAnnotationMulti(AnnotationsDataProvider::ANNOTATION_SIMPLE_NAME, self::class, "data")
        );
    }

    #[Skip()]
    private function method(): void
    {
    }

    #[DataProviderExternal(ExternalDataProvider::class, "dataProviderArray")]
    private function dataProviderExternal(): void
    {
    }

    #[Data(["abc", "def", ])]
    #[Data(["ghi", "jkl", ])]
    private function data(): void
    {
    }
}
