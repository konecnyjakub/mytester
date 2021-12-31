<?php

declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\DataProvider as DataProviderAttribute;
use MyTester\Attributes\Skip;
use MyTester\Attributes\Test;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class TestCase
 *
 * @author Jakub Konečný
 */
#[TestSuite("TestCase")]
final class TestCaseTest extends TestCase
{
    /** @var bool|int */
    private $one = false;

    public function setUp(): void
    {
        $this->one = 1;
    }

    public function tearDown(): void
    {
        $this->one = false;
    }

    public function shutDown(): void
    {
        $this->assertFalsey($this->one);
    }

    public function testState(): void
    {
        $this->assertFalse($this->shouldFail);
        $this->assertSame(1, $this->getCounter());
        $this->incCounter();
        $this->assertSame(3, $this->getCounter());
        $this->resetCounter();
        $this->assertSame(0, $this->getCounter());
    }

    /**
     * Test parameters
     */
    #[DataProviderAttribute("dataProvider")]
    public function testParams(string $text): void
    {
        $this->assertType("string", $text);
        $this->assertContains("a", $text);
    }

    public function testParamsNoneProvided(string $text): void
    {
        $this->assertTrue(false);
    }

    public function dataProvider(): array
    {
        return ["abc", "adef", ];
    }

    /**
     * Test custom test's name
     */
    #[Test("Custom name")]
    public function testTestName(): void
    {
        $this->assertSame(1, $this->one);
    }

    /**
     * Test unconditional skipping
     */
    #[Test("Skip")]
    #[Skip()]
    public function testSkip(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on boolean
     */
    #[Test("Boolean")]
    #[Skip(true)]
    public function testSkipBoolean(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on integer
     */
    #[Test("Integer")]
    #[Skip(1)]
    public function testSkipInteger(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on float
     */
    #[Test("Integer")]
    #[Skip(1.5)]
    public function testSkipFloat(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on string
     */
    #[Test("String")]
    #[Skip("abc")]
    public function testSkipString(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on PHP version
     */
    #[Test("PHP version")]
    #[Skip(["php" => 666])]
    public function testSkipPhpVersion(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on sapi
     */
    #[Test("CGI sapi")]
    #[Skip(["sapi" => "abc"])]
    public function testCgiSapi(): void
    {
        $this->assertNotSame(PHP_SAPI, "abc");
    }

    /**
     * Test skipping based on loaded extension
     */
    #[Test("Extension")]
    #[Skip(["extension" => "abc"])]
    public function testSkipExtension(): void
    {
        $this->assertTrue(false);
    }
}
