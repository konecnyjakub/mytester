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
        $this->assertFalse(false);
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
    #[Test("Float")]
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

    #[Test("No assertions")]
    public function testNoAssertions(): void
    {
    }

    public function testGetSuiteName(): void
    {
        $this->assertSame("TestCase", $this->getSuiteName());
    }

    public function testGetJobName(): void
    {
        $this->assertSame("TestCase::" . __FUNCTION__, $this->getJobName(self::class, __FUNCTION__));
        $this->assertSame("Extension", $this->getJobName(self::class, "testSkipExtension"));
    }

    public function testGetJobs(): void
    {
        $jobs = $this->getJobs();
        $this->assertCount(18, $jobs);

        $job = $jobs[0];
        $this->assertSame("TestCase::testState", $job->name);
        $this->assertSame([$this, "testState", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[1];
        $this->assertSame("TestCase::testParams", $job->name);
        $this->assertSame([$this, "testParams", ], $job->callback);
        $this->assertSame(["abc", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[2];
        $this->assertSame("TestCase::testParams", $job->name);
        $this->assertSame([$this, "testParams", ], $job->callback);
        $this->assertSame(["adef", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[3];
        $this->assertSame("TestCase::testParamsNoneProvided", $job->name);
        $this->assertSame([$this, "testParamsNoneProvided", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertSame("Method requires at least 1 parameter but data provider does not provide any.", $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[4];
        $this->assertSame("Custom name", $job->name);
        $this->assertSame([$this, "testTestName", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[5];
        $this->assertSame("Skip", $job->name);
        $this->assertSame([$this, "testSkip", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[6];
        $this->assertSame("Boolean", $job->name);
        $this->assertSame([$this, "testSkipBoolean", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[7];
        $this->assertSame("Integer", $job->name);
        $this->assertSame([$this, "testSkipInteger", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[8];
        $this->assertSame("Float", $job->name);
        $this->assertSame([$this, "testSkipFloat", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[9];
        $this->assertSame("String", $job->name);
        $this->assertSame([$this, "testSkipString", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[10];
        $this->assertSame("PHP version", $job->name);
        $this->assertSame([$this, "testSkipPhpVersion", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[11];
        $this->assertSame("CGI sapi", $job->name);
        $this->assertSame([$this, "testCgiSapi", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[12];
        $this->assertSame("Extension", $job->name);
        $this->assertSame([$this, "testSkipExtension", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[13];
        $this->assertSame("No assertions", $job->name);
        $this->assertSame([$this, "testNoAssertions", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[14];
        $this->assertSame("TestCase::testGetSuiteName", $job->name);
        $this->assertSame([$this, "testGetSuiteName", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[15];
        $this->assertSame("TestCase::testGetJobName", $job->name);
        $this->assertSame([$this, "testGetJobName", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[16];
        $this->assertSame("TestCase::testGetJobs", $job->name);
        $this->assertSame([$this, "testGetJobs", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[17];
        $this->assertSame("TestCase::testIncomplete", $job->name);
        $this->assertSame([$this, "testIncomplete", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);
    }

    public function testIncomplete(): void
    {
        $this->assertThrowsException(function () {
            $this->markTestIncomplete();
        }, IncompleteTestException::class, "");
        $this->assertThrowsException(function () {
            $this->markTestIncomplete("abc");
        }, IncompleteTestException::class, "abc");
        $this->markTestIncomplete("test");
        $this->assertTrue(false);
    }
}
