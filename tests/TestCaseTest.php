<?php

declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Attributes\DataProvider;
use MyTester\Annotations\Attributes\Skip;
use MyTester\Annotations\Attributes\Test;
use MyTester\Annotations\Attributes\TestSuite;

/**
 * Test suite for class TestCase
 *
 * @testSuite TestCase
 * @author Jakub Konečný
 * @property-read bool|int $one
 */
#[TestSuite("TestCase")]
final class TestCaseTest extends TestCase
{
    /** @var bool|int */
    private $one = false;

    /**
     * @return bool|int
     */
    public function getOne()
    {
        return $this->one;
    }

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
     *
     * @dataProvider(dataProvider)
     */
    #[DataProvider("dataProvider")]
    public function testParams(string $text): void
    {
        $this->assertType("string", $text);
        $this->assertContains("a", $text);
    }

    public function dataProvider(): array
    {
        return ["abc", "adef",];
    }

    /**
     * Test custom test's name
     *
     * @test Custom name
     */
    #[Test("Custom name")]
    public function testTestName(): void
    {
        $this->assertSame(1, $this->one);
    }

    /**
     * Test unconditional skipping
     *
     * @test Skip
     * @skip
     */
    #[Test("Skip")]
    #[Skip()]
    public function testSkip(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on boolean
     *
     * @test Boolean
     * @skip(true)
     */
    #[Test("Boolean")]
    #[Skip(true)]
    public function testSkipBoolean(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on integer
     *
     * @test Integer
     * @skip(1)
     */
    #[Test("Integer")]
    #[Skip(1)]
    public function testSkipInteger(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on float
     *
     * @test Float
     * @skip(1.5)
     */
    #[Test("Integer")]
    #[Skip(1.5)]
    public function testSkipFloat(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on string
     *
     * @test String
     * @skip(abc)
     */
    #[Test("String")]
    #[Skip("abc")]
    public function testSkipString(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on PHP version
     *
     * @test PHP version
     * @skip(php=666)
     */
    #[Test("PHP version")]
    #[Skip(["php" => 666])]
    public function testSkipPhpVersion(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on sapi
     *
     * @test CGI sapi
     * @skip(sapi=abc)
     */
    #[Test("CGI sapi")]
    #[Skip(["sapi" => "abc"])]
    public function testCgiSapi(): void
    {
        $this->assertNotSame(PHP_SAPI, "abc");
    }

    /**
     * Test skipping based on loaded extension
     *
     * @test Extension
     * @skip(extension=abc)
     */
    #[Test("Extension")]
    #[Skip(["extension" => "abc"])]
    public function testSkipExtension(): void
    {
        $this->assertTrue(false);
    }
}
