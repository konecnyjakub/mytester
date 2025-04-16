<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class AnnotationsEngine
 *
 * @author Jakub Konečný
 */
#[TestSuite("PHPT a nnotations engine")]
#[Group("phpt")]
final class AnnotationsEngineTest extends TestCase
{
    public function testHasAnnotation(): void
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . "test.phpt";
        $engine = new AnnotationsEngine(new Parser());
        $this->assertTrue($engine->hasAnnotation(self::ANNOTATION_TEST, $filename));
        $this->assertFalse($engine->hasAnnotation(Parser::SECTION_ARGS, $filename));

        $filename = __DIR__ . DIRECTORY_SEPARATOR . "non-existing.phpt";
        $this->assertFalse($engine->hasAnnotation(self::ANNOTATION_TEST, $filename));
        $this->assertFalse($engine->hasAnnotation(Parser::SECTION_ARGS, $filename));
    }

    public function testGetAnnotation(): void
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . "test.phpt";
        $engine = new AnnotationsEngine(new Parser());
        $this->assertSame("Test", $engine->getAnnotation(self::ANNOTATION_TEST, $filename));
        $this->assertSame([], $engine->getAnnotation(AnnotationsEngine::ANNOTATION_ENV, $filename));
        $this->assertNull($engine->getAnnotation(Parser::SECTION_ARGS, $filename));

        $filename = __DIR__ . DIRECTORY_SEPARATOR . "test_env.phpt";
        $this->assertSame("Test env", $engine->getAnnotation(self::ANNOTATION_TEST, $filename));
        $this->assertSame(["one" => "abc", ], $engine->getAnnotation(AnnotationsEngine::ANNOTATION_ENV, $filename));
        $this->assertNull($engine->getAnnotation(Parser::SECTION_ARGS, $filename));

        $filename = __DIR__ . DIRECTORY_SEPARATOR . "non-existing.phpt";
        $this->assertNull($engine->getAnnotation(self::ANNOTATION_TEST, $filename));
        $this->assertSame([], $engine->getAnnotation(AnnotationsEngine::ANNOTATION_ENV, $filename));
        $this->assertNull($engine->getAnnotation(Parser::SECTION_ARGS, $filename));
    }

    public function testGetAnnotationMulti(): void
    {
        $filename = __DIR__ . DIRECTORY_SEPARATOR . "test.phpt";
        $engine = new AnnotationsEngine(new Parser());
        $this->assertSame(["Test"], $engine->getAnnotationMulti(self::ANNOTATION_TEST, $filename));
        $this->assertSame([], $engine->getAnnotationMulti(Parser::SECTION_ARGS, $filename));

        $filename = __DIR__ . DIRECTORY_SEPARATOR . "non-existing.phpt";
        $this->assertSame([], $engine->getAnnotationMulti(self::ANNOTATION_TEST, $filename));
        $this->assertSame([], $engine->getAnnotationMulti(Parser::SECTION_ARGS, $filename));
    }
}
