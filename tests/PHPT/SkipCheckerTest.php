<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class SkipChecker
 *
 * @author Jakub Konečný
 */
#[TestSuite("PHPT skip checker")]
#[Group("phpt")]
final class SkipCheckerTest extends TestCase
{
    public function testShouldSkip(): void
    {
        $parser = new Parser();
        $runner = new Runner();
        $filename = __DIR__ . DIRECTORY_SEPARATOR . "skipped_test.phpt";
        $annotationsEngine = new AnnotationsEngine($parser);
        $skipChecker = new SkipChecker($annotationsEngine, $runner);
        $this->assertSame(true, $skipChecker->shouldSkip($filename));

        $filename = __DIR__ . DIRECTORY_SEPARATOR . "test.phpt";
        $annotationsEngine = new AnnotationsEngine($parser);
        $skipChecker = new SkipChecker($annotationsEngine, $runner);
        $this->assertSame(false, $skipChecker->shouldSkip($filename));
    }
}
