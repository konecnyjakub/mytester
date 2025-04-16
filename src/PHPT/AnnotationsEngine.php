<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use MyTester\Annotations\IAnnotationsReaderEngine;
use MyTester\TestCase;

/**
 * PHPT engine for annotations reader
 *
 * @author Jakub Konečný
 */
final readonly class AnnotationsEngine implements IAnnotationsReaderEngine
{
    public const string ANNOTATION_SKIP = "skip";
    public const string ANNOTATION_INPUT = "input";
    public const string ANNOTATION_ENV = "env";
    public const string ANNOTATION_FILE = "file";
    public const string ANNOTATION_EXPECT = "expect";
    public const string ANNOTATION_EXPECT_EXTERNAL = "expect_external";
    public const string ANNOTATION_EXPECT_REGEX = "expect_regex";
    public const string ANNOTATION_CLEAN = "clean";

    private const array ANNOTATION_TO_SECTION_NAME = [
        TestCase::ANNOTATION_TEST => Parser::SECTION_TEST,
        self::ANNOTATION_SKIP => Parser::SECTION_SKIPIF,
        self::ANNOTATION_INPUT => Parser::SECTION_STDIN,
        self::ANNOTATION_ENV => Parser::SECTION_ENV,
        self::ANNOTATION_FILE => Parser::SECTION_FILE,
        self::ANNOTATION_EXPECT => Parser::SECTION_EXPECT,
        self::ANNOTATION_EXPECT_EXTERNAL => Parser::SECTION_EXPECT_EXTERNAL,
        self::ANNOTATION_EXPECT_REGEX => Parser::SECTION_EXPECTREGEX,
        self::ANNOTATION_CLEAN => Parser::SECTION_CLEAN,
    ];

    public function __construct(private Parser $parser)
    {
    }

    /**
     * @param object|string $class filename
     */
    public function hasAnnotation(string $name, object|string $class, ?string $method = null): bool
    {
        $sectionName = $this->getSectionName($name);
        $sections = $this->parser->parse($class); // @phpstan-ignore argument.type
        return $sectionName !== null && array_key_exists($sectionName, $sections);
    }

    /**
     * @param object|string $class filename
     */
    public function getAnnotation(string $name, object|string $class, ?string $method = null): mixed
    {
        $sectionName = $this->getSectionName($name);
        $sections = $this->parser->parse($class); // @phpstan-ignore argument.type
        if ($sectionName !== null && array_key_exists($sectionName, $sections)) {
            return $sections[$sectionName];
        }
        return match ($sectionName) {
            Parser::SECTION_ENV => [],
            default => null,
        };
    }

    /**
     * @param object|string $class filename
     */
    public function getAnnotationMulti(string $name, object|string $class, ?string $method = null): array
    {
        $section = $this->getAnnotation($name, $class, $method);
        if ($section === null) {
            return [];
        }
        return [$section];
    }

    private function getSectionName(string $annotationName): ?string
    {
        if (!array_key_exists($annotationName, self::ANNOTATION_TO_SECTION_NAME)) {
            return null;
        }
        return self::ANNOTATION_TO_SECTION_NAME[$annotationName];
    }
}
