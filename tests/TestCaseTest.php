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
    private int|bool $one = false;

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

    #[DataProviderAttribute("dataProvider")]
    public function testParamsNotEnough(string $text, int $number): void
    {
        $this->assertTrue(false);
    }

    #[DataProviderAttribute("dataProviderMulti")]
    public function testParamsMulti(string $text, int $number): void
    {
        $this->assertContains("a", $text);
        $this->assertGreaterThan(0, $number);
    }

    public function dataProvider(): array
    {
        return ["abc", "adef", ];
    }

    public function dataProviderMulti(): array
    {
        return [
            ["abc", 1, ],
            ["abcd", 2, ],
        ];
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

    /**
     * Test skipping based on os family
     */
    #[Test("OS family")]
    #[Skip(["osFamily" => "Solaris"])]
    public function testSkipOsFamily(): void
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
        $this->assertSame("Assertions", $this->getSuiteName(AssertTest::class));
        $this->assertSame(\stdClass::class, $this->getSuiteName(\stdClass::class));
    }

    public function testGetJobName(): void
    {
        $this->assertSame("TestCase::" . __FUNCTION__, $this->getJobName(self::class, __FUNCTION__));
        $this->assertSame("Extension", $this->getJobName(self::class, "testSkipExtension"));
    }

    public function testGetTestMethodsNames(): void
    {
        $methods = $this->getTestMethodsNames();
        $this->assertSame(
            [
                "testState",
                "testParams",
                "testParamsNoneProvided",
                "testParamsNotEnough",
                "testParamsMulti",
                "testTestName",
                "testSkip",
                "testSkipPhpVersion",
                "testCgiSapi",
                "testSkipExtension",
                "testSkipOsFamily",
                "testNoAssertions",
                "testGetSuiteName",
                "testGetJobName",
                "testGetTestMethodsNames",
                "testGetJobs",
                "testIncomplete",
                "testSkipInside",
                "testWhatever",
            ],
            $methods
        );
    }

    public function testGetJobs(): void
    {
        $jobs = $this->getJobs();
        $this->assertCount(21, $jobs);

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
        $this->assertSame("TestCase::testParamsNotEnough", $job->name);
        $this->assertSame([$this, "testParamsNotEnough", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertSame("Method requires at least 2 parameter(s) but data provider provides only 0.", $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[5];
        $this->assertSame("TestCase::testParamsMulti", $job->name);
        $this->assertSame([$this, "testParamsMulti", ], $job->callback);
        $this->assertSame(["abc", 1, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[6];
        $this->assertSame("TestCase::testParamsMulti", $job->name);
        $this->assertSame([$this, "testParamsMulti", ], $job->callback);
        $this->assertSame(["abcd", 2, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[7];
        $this->assertSame("Custom name", $job->name);
        $this->assertSame([$this, "testTestName", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[8];
        $this->assertSame("Skip", $job->name);
        $this->assertSame([$this, "testSkip", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[9];
        $this->assertSame("PHP version", $job->name);
        $this->assertSame([$this, "testSkipPhpVersion", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[10];
        $this->assertSame("CGI sapi", $job->name);
        $this->assertSame([$this, "testCgiSapi", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[11];
        $this->assertSame("Extension", $job->name);
        $this->assertSame([$this, "testSkipExtension", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[12];
        $this->assertSame("OS family", $job->name);
        $this->assertSame([$this, "testSkipOsFamily", ], $job->callback);
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
        $this->assertSame("TestCase::testGetTestMethodsNames", $job->name);
        $this->assertSame([$this, "testGetTestMethodsNames", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[17];
        $this->assertSame("TestCase::testGetJobs", $job->name);
        $this->assertSame([$this, "testGetJobs", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[18];
        $this->assertSame("TestCase::testIncomplete", $job->name);
        $this->assertSame([$this, "testIncomplete", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[19];
        $this->assertSame("TestCase::testSkipInside", $job->name);
        $this->assertSame([$this, "testSkipInside", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(1, $job->onAfterExecute);

        $job = $jobs[20];
        $this->assertSame("TestCase::testWhatever", $job->name);
        $this->assertSame([$this, "testWhatever", ], $job->callback);
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

    public function testSkipInside(): void
    {
        $this->assertThrowsException(function () {
            $this->markTestSkipped();
        }, SkippedTestException::class, "");
        $this->assertThrowsException(function () {
            $this->markTestSkipped("abc");
        }, SkippedTestException::class, "abc");
        $this->markTestSkipped("test");
        $this->assertTrue(false);
    }

    /**
     * The last test method in this class has to executed so {@see self::shutDown()} does not report a failure
     */
    public function testWhatever(): void
    {
        $this->assertTrue(true);
    }
}
