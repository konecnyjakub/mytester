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
        return $this->skipChecker;
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
        $this->assertType("string", $this->getSkipChecker()->checkPhpSapi("abc"));
    }

    public function testGetSkipValue(): void
    {
        $this->assertNull($this->getSkipChecker()->getSkipValue(static::class, "skipNull"));
        $this->assertTrue($this->getSkipChecker()->getSkipValue(static::class, "skip"));
        $this->assertSame(false, $this->getSkipChecker()->getSkipValue(static::class, "skipFalse"));
        $this->assertSame(1.5, $this->getSkipChecker()->getSkipValue(static::class, "skipFloat"));
        $this->assertSame("abc", $this->getSkipChecker()->getSkipValue(static::class, "skipString"));
        $array = $this->getSkipChecker()->getSkipValue(static::class, "skipArray");
        $this->assertType("iterable", $array);
        $this->assertCount(1, $array);
    }

    public function testShouldSkip(): void
    {
        $this->assertFalsey($this->getSkipChecker()->shouldSkip(static::class, "skipNull"));
        $this->assertTruthy($this->getSkipChecker()->shouldSkip(static::class, "skip"));
        $this->assertFalsey($this->getSkipChecker()->shouldSkip(static::class, "skipFalse"));
        $this->assertTruthy($this->getSkipChecker()->shouldSkip(static::class, "skipInteger"));
        $this->assertTruthy($this->getSkipChecker()->shouldSkip(static::class, "skipFloat"));
        $this->assertTruthy($this->getSkipChecker()->shouldSkip(static::class, "skipString"));
        $this->assertTruthy($this->getSkipChecker()->shouldSkip(static::class, "skipArray"));
    }

    private function skipNull(): void
    {
    }

    #[Skip()]
    private function skip(): void
    {
    }

    #[Skip(false)]
    private function skipFalse(): void
    {
    }

    #[Skip(1)]
    private function skipInteger(): void
    {
    }

    #[Skip(1.5)]
    private function skipFloat(): void
    {
    }

    #[Skip("abc")]
    private function skipString(): void
    {
    }

    #[Skip(["php" => 666])]
    private function skipArray(): void
    {
    }
}
