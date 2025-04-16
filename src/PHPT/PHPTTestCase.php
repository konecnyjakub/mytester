<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use MyTester\TestCase;

/**
 * Test suite that runs a .phpt file
 *
 * @author Jakub Konečný
 */
abstract class PHPTTestCase extends TestCase
{
    private readonly AnnotationsEngine $annotationsEngine;

    private readonly Runner $runner;

    public function __construct(private readonly Parser $parser, private readonly string $filename)
    {
        parent::__construct();
        $this->annotationsEngine = new AnnotationsEngine($this->parser);
        $this->runner = new Runner();
        $this->annotationsReader->registerEngine($this->annotationsEngine);
        $this->skipChecker = new SkipChecker($this->annotationsEngine, $this->runner);
    }

    public function tearDown(): void
    {
        /** @var string $clean */
        $clean = $this->annotationsEngine->getAnnotation(AnnotationsEngine::ANNOTATION_CLEAN, $this->filename) ?? "";
        if ($clean !== "") {
            $this->runner->runCode($clean);
        }
    }

    protected function shouldSkip(string $methodName): bool
    {
        /** @var SkipChecker $skipChecker */
        $skipChecker = $this->skipChecker;
        return $skipChecker->shouldSkip($this->filename);
    }

    public function testFile(): void
    {
        /** @var string $code */
        $code = $this->annotationsEngine->getAnnotation(AnnotationsEngine::ANNOTATION_FILE, $this->filename) ?? "";
        /** @var array<string, string|int|float> $env */
        $env = $this->annotationsEngine->getAnnotation(AnnotationsEngine::ANNOTATION_ENV, $this->filename);
        /** @var string $input */
        $input = $this->annotationsEngine->getAnnotation(AnnotationsEngine::ANNOTATION_INPUT, $this->filename) ?? "";

        $actualOutput = $this->runner->runCode($code, [], $env, "", $input);

        $expectedOutput = $this->annotationsEngine->getAnnotation(
            AnnotationsEngine::ANNOTATION_EXPECT,
            $this->filename
        );
        if (is_string($expectedOutput)) {
            $this->assertSame($expectedOutput, $actualOutput);
        }

        $expectedOutputFile = $this->annotationsEngine->getAnnotation(
            AnnotationsEngine::ANNOTATION_EXPECT_EXTERNAL,
            $this->filename
        );
        if (is_string($expectedOutputFile)) {
            $this->assertMatchesFile($expectedOutputFile, $actualOutput);
        }

        $expectedOutputRegex = $this->annotationsEngine->getAnnotation(
            AnnotationsEngine::ANNOTATION_EXPECT_REGEX,
            $this->filename
        );
        if (is_string($expectedOutputRegex)) {
            $this->assertMatchesRegExp($expectedOutputRegex, $actualOutput);
        }
    }
}
