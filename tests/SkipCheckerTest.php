<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\Skip;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class SkipChecker
 *
 * @author Jakub Konečný
 */
#[TestSuite("SkipChecker")]
final class SkipCheckerTest extends TestCase
{
    private function getSkipChecker(): SkipChecker
    {
        return (new SkipChecker($this->annotationsReader));
    }

    public function testCheckPhpVersion(): void
    {
        $this->assertNull($this->getSkipChecker()->checkPhpVersion(1));
        $this->assertType("string", $this->getSkipChecker()->checkPhpVersion(PHP_INT_MAX));
    }

    public function testCheckLoadedExtension(): void
    {
        $this->assertNull($this->getSkipChecker()->checkLoadedExtension("ctype"));
        $this->assertType("string", $this->getSkipChecker()->checkLoadedExtension("abc"));
    }

    public function testCheckPhpSapi(): void
    {
        $this->assertNull($this->getSkipChecker()->checkPhpSapi(PHP_SAPI));
        $this->assertType("string", $this->getSkipChecker()->checkPhpSapi("abc"));
    }

    public function testGetSkipValue(): void
    {
        $this->assertNull($this->getSkipChecker()->getSkipValue(static::class, "skipNull"));
        $this->assertSame([], $this->getSkipChecker()->getSkipValue(static::class, "skip"));
        $this->assertSame(["php" => 666, ], $this->getSkipChecker()->getSkipValue(static::class, "skipArray"));
    }

    public function testShouldSkip(): void
    {
        $this->assertSame(false, $this->getSkipChecker()->shouldSkip(static::class, "skipNull"));
        $this->assertTruthy($this->getSkipChecker()->shouldSkip(static::class, "skip"));
        $this->assertTruthy($this->getSkipChecker()->shouldSkip(static::class, "skipArray"));
        $this->assertFalsey($this->getSkipChecker()->shouldSkip(static::class, "skipArrayUnknown"));
    }

    private function skipNull(): void
    {
    }

    #[Skip()]
    private function skip(): void
    {
    }

    #[Skip(["php" => 666])]
    private function skipArray(): void
    {
    }

    #[Skip(["abc" => "def"])]
    private function skipArrayUnknown(): void
    {
    }
}
