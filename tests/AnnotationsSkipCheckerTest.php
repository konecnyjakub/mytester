<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\RequiresOsFamily;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class AnnotationsSkipChecker
 *
 * @author Jakub Konečný
 */
#[TestSuite("AnnotationsSkipChecker")]
final class AnnotationsSkipCheckerTest extends TestCase
{
    private function getSkipChecker(): AnnotationsSkipChecker
    {
        return (new AnnotationsSkipChecker($this->annotationsReader));
    }

    public function testShouldSkip(): void
    {
        $this->assertSame(false, $this->getSkipChecker()->shouldSkip(self::class, "notSkip"));
        $this->assertSame(true, $this->getSkipChecker()->shouldSkip(self::class, "skip"));
        $this->assertSame(
            "PHP version is lesser than 666",
            $this->getSkipChecker()->shouldSkip(self::class, "skipPhpVersion")
        );
        $this->assertSame(false, $this->getSkipChecker()->shouldSkip(self::class, "notSkipPhpVersion"));
        $this->assertSame(
            "os family is not Solaris",
            $this->getSkipChecker()->shouldSkip(self::class, "skipOsFamily")
        );
    }

    private function notSkip(): void
    {
    }

    #[Skip()]
    private function skip(): void
    {
    }

    #[RequiresPhpVersion("666")]
    private function skipPhpVersion(): void
    {
    }

    #[RequiresPhpVersion("8.3")]
    private function notSkipPhpVersion(): void
    {
    }

    #[RequiresOsFamily("Solaris")]
    private function skipOsFamily(): void
    {
    }
}
