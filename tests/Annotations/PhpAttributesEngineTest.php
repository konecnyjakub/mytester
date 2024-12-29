<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use MyTester\Attributes\DataProviderExternal;
use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;
use MyTester\DataProvider;
use MyTester\ExternalDataProvider;
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
    public function testHasAnnotation(): void
    {
        $engine = new PhpAttributesEngine();
        $this->assertTrue($engine->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertFalse($engine->hasAnnotation(SkipChecker::ANNOTATION_NAME, self::class));
        $this->assertTrue($engine->hasAnnotation(SkipChecker::ANNOTATION_NAME, self::class, "method"));
        $this->assertFalse($engine->hasAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class, "method"));
    }

    public function testGetAnnotation(): void
    {
        $engine = new PhpAttributesEngine();
        $this->assertNull($engine->getAnnotation(SkipChecker::ANNOTATION_NAME, self::class));
        $this->assertSame("PhpAttributesEngine", $engine->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class));
        $this->assertNull($engine->getAnnotation(TestCase::ANNOTATION_TEST_SUITE, self::class, "method"));
        $this->assertSame("", $engine->getAnnotation(SkipChecker::ANNOTATION_NAME, self::class, "method"));
        $this->assertSame(
            ExternalDataProvider::class . "::dataProviderArray",
            $engine->getAnnotation(DataProvider::ANNOTATION_EXTERNAL_NAME, self::class, "dataProviderExternal")
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
}
