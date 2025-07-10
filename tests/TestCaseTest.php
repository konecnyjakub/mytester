<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventDispatcher;
use MyTester\Attributes\AfterTest;
use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Data;
use MyTester\Attributes\DataProvider as DataProviderAttribute;
use MyTester\Attributes\DataProviderExternal;
use MyTester\Attributes\IgnoreDeprecations;
use MyTester\Attributes\NoAssertions;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\RequiresOsFamily;
use MyTester\Attributes\RequiresPackage;
use MyTester\Attributes\RequiresPhpExtension;
use MyTester\Attributes\RequiresPhpVersion;
use MyTester\Attributes\RequiresSapi;
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
    private int $one = 0;

    public function setUp(): void
    {
        $this->one++;
    }

    #[BeforeTest]
    public function customSetUp(): void
    {
        $this->one++;
    }

    public function tearDown(): void
    {
        $this->one--;
    }

    #[AfterTest]
    public function customTearDown(): void
    {
        $this->one = 0;
    }

    public function shutDown(): void
    {
        $this->assertSame(0, $this->one);
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

    #[DataProviderAttribute("dataProviderIterator")]
    public function testParamsIterator(int $number): void
    {
        $this->assertGreaterThan(0, $number);
    }

    #[DataProviderExternal(ExternalDataProvider::class, "dataProviderArray")]
    public function testParamsExternal(string $text, int $number): void
    {
        $this->assertContains("a", $text);
        $this->assertGreaterThan(0, $number);
    }

    #[Data(["abc", ])]
    #[Data(["abcd", ])]
    public function testParamsData(string $text): void
    {
        $this->assertContains("a", $text);
    }

    /**
     * @return array<int, string>[]
     */
    public function dataProvider(): array
    {
        return [["abc"], ["adef"], ];
    }

    /**
     * @return array<int|string, array{0: string, 1: int}>
     */
    public function dataProviderMulti(): array
    {
        return [
            "first" => ["abc", 1, ],
            ["abcd", 2, ],
        ];
    }

    /**
     * @return iterable<int|string, int[]>
     */
    public function dataProviderIterator(): iterable
    {
        yield "first" => [1, ];
        yield [2, ];
    }

    /**
     * Test custom test's name
     */
    #[Test("Custom name")]
    public function testTestName(): void
    {
        $this->assertSame(2, $this->one);
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
    #[RequiresPhpVersion("666")]
    public function testSkipPhpVersion(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on sapi
     */
    #[Test("CGI sapi")]
    #[RequiresSapi("cgi")]
    public function testCgiSapi(): void
    {
        $this->assertNotSame(PHP_SAPI, "abc");
    }

    /**
     * Test skipping based on loaded extension
     */
    #[Test("Extension")]
    #[RequiresPhpExtension("xml")]
    #[RequiresPhpExtension("abc")]
    public function testSkipExtension(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on os family
     */
    #[Test("OS family")]
    #[RequiresOsFamily("Solaris")]
    public function testSkipOsFamily(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on installed package
     */
    #[Test("Package")]
    #[RequiresPackage("phpstan/phpstan", "^1.0")]
    public function testSkipPackage(): void
    {
        $this->assertTrue(false);
    }

    /**
     * Test skipping based on env variable
     */
    #[Test("Env variable")]
    #[RequiresEnvVariable("ENV")]
    public function testSkipEnvVariable(): void
    {
        $this->assertTrue(false);
    }

    #[Test("No assertions")]
    #[NoAssertions]
    public function testNoAssertions(): void
    {
    }

    public function testShouldCheckAssertions(): void
    {
        $this->assertTrue($this->shouldCheckAssertions(__FUNCTION__));
        $this->assertFalse($this->shouldCheckAssertions("testNoAssertions"));
    }

    #[Test("Deprecation")]
    #[RequiresPhpVersion("8.4")]
    #[IgnoreDeprecations]
    public function testDeprecation(): void
    {
        $rp = new \ReflectionProperty(TestCase::class, "eventDispatcher");
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $rp->getValue($this);
        $job = new Job("Test deprecation", function () {
            $this->deprecatedMethod(); // @phpstan-ignore method.deprecated
        });
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::WARNING, $job->result);
        $this->assertContains("deprecated", $job->output);
        $this->deprecatedMethod(); // @phpstan-ignore method.deprecated
    }

    #[\Deprecated("test")]
    private function deprecatedMethod(): void
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
                "testParamsIterator",
                "testParamsExternal",
                "testParamsData",
                "testTestName",
                "testSkip",
                "testSkipPhpVersion",
                "testCgiSapi",
                "testSkipExtension",
                "testSkipOsFamily",
                "testSkipPackage",
                "testSkipEnvVariable",
                "testNoAssertions",
                "testShouldCheckAssertions",
                "testDeprecation",
                "testGetSuiteName",
                "testGetJobName",
                "testGetTestMethodsNames",
                "testShouldReportDeprecations",
                "testGetJobs",
                "testIncomplete",
                "testSkipInside",
                "testWhatever",
            ],
            $methods
        );
    }

    public function testShouldReportDeprecations(): void
    {
        $this->assertTrue($this->shouldReportDeprecations(__FUNCTION__));
        $this->assertFalse($this->shouldReportDeprecations("testDeprecation"));
    }

    public function testGetJobs(): void
    {
        $jobs = $this->getJobs();
        $this->assertCount(32, $jobs);

        $job = $jobs[0];
        $this->assertSame("TestCase::testState", $job->name);
        $this->assertSame([$this, "testState", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[1];
        $this->assertSame("TestCase::testParams", $job->name);
        $this->assertSame([$this, "testParams", ], $job->callback);
        $this->assertSame(["abc", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[2];
        $this->assertSame("TestCase::testParams", $job->name);
        $this->assertSame([$this, "testParams", ], $job->callback);
        $this->assertSame(["adef", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[3];
        $this->assertSame("TestCase::testParamsNoneProvided", $job->name);
        $this->assertSame([$this, "testParamsNoneProvided", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertSame("Method requires at least 1 parameter but data provider does not provide any.", $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[4];
        $this->assertSame("TestCase::testParamsNotEnough", $job->name);
        $this->assertSame([$this, "testParamsNotEnough", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertSame("Method requires at least 2 parameter(s) but data provider provides only 1.", $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[5];
        $this->assertSame("TestCase::testParamsMulti", $job->name);
        $this->assertSame([$this, "testParamsMulti", ], $job->callback);
        $this->assertSame(["abc", 1, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("first", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[6];
        $this->assertSame("TestCase::testParamsMulti", $job->name);
        $this->assertSame([$this, "testParamsMulti", ], $job->callback);
        $this->assertSame(["abcd", 2, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[7];
        $this->assertSame("TestCase::testParamsIterator", $job->name);
        $this->assertSame([$this, "testParamsIterator", ], $job->callback);
        $this->assertSame([1, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("first", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[8];
        $this->assertSame("TestCase::testParamsIterator", $job->name);
        $this->assertSame([$this, "testParamsIterator", ], $job->callback);
        $this->assertSame([2, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[9];
        $this->assertSame("TestCase::testParamsExternal", $job->name);
        $this->assertSame([$this, "testParamsExternal", ], $job->callback);
        $this->assertSame(["abc", 1, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("first", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[10];
        $this->assertSame("TestCase::testParamsExternal", $job->name);
        $this->assertSame([$this, "testParamsExternal", ], $job->callback);
        $this->assertSame(["abcd", 2, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[11];
        $this->assertSame("TestCase::testParamsData", $job->name);
        $this->assertSame([$this, "testParamsData", ], $job->callback);
        $this->assertSame(["abc", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[12];
        $this->assertSame("TestCase::testParamsData", $job->name);
        $this->assertSame([$this, "testParamsData", ], $job->callback);
        $this->assertSame(["abcd", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[13];
        $this->assertSame("Custom name", $job->name);
        $this->assertSame([$this, "testTestName", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[14];
        $this->assertSame("Skip", $job->name);
        $this->assertSame([$this, "testSkip", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[15];
        $this->assertSame("PHP version", $job->name);
        $this->assertSame([$this, "testSkipPhpVersion", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[16];
        $this->assertSame("CGI sapi", $job->name);
        $this->assertSame([$this, "testCgiSapi", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[17];
        $this->assertSame("Extension", $job->name);
        $this->assertSame([$this, "testSkipExtension", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[18];
        $this->assertSame("OS family", $job->name);
        $this->assertSame([$this, "testSkipOsFamily", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[19];
        $this->assertSame("Package", $job->name);
        $this->assertSame([$this, "testSkipPackage", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[20];
        $this->assertSame("Env variable", $job->name);
        $this->assertSame([$this, "testSkipEnvVariable", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[21];
        $this->assertSame("No assertions", $job->name);
        $this->assertSame([$this, "testNoAssertions", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[22];
        $this->assertSame("TestCase::testShouldCheckAssertions", $job->name);
        $this->assertSame([$this, "testShouldCheckAssertions", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[23];
        $this->assertSame("Deprecation", $job->name);
        $this->assertSame([$this, "testDeprecation", ], $job->callback);
        $this->assertSame([], $job->params);
        if (version_compare(PHP_VERSION, "8.4.0") >= 0) {
            $this->assertFalse((bool) $job->skip);
        } else {
            $this->assertSame("PHP version is lesser than 8.4", $job->skip);
        }
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertFalse($job->reportDeprecations);

        $job = $jobs[24];
        $this->assertSame("TestCase::testGetSuiteName", $job->name);
        $this->assertSame([$this, "testGetSuiteName", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[25];
        $this->assertSame("TestCase::testGetJobName", $job->name);
        $this->assertSame([$this, "testGetJobName", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[26];
        $this->assertSame("TestCase::testGetTestMethodsNames", $job->name);
        $this->assertSame([$this, "testGetTestMethodsNames", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[27];
        $this->assertSame("TestCase::testShouldReportDeprecations", $job->name);
        $this->assertSame([$this, "testShouldReportDeprecations", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[28];
        $this->assertSame("TestCase::testGetJobs", $job->name);
        $this->assertSame([$this, "testGetJobs", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[29];
        $this->assertSame("TestCase::testIncomplete", $job->name);
        $this->assertSame([$this, "testIncomplete", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[30];
        $this->assertSame("TestCase::testSkipInside", $job->name);
        $this->assertSame([$this, "testSkipInside", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);

        $job = $jobs[31];
        $this->assertSame("TestCase::testWhatever", $job->name);
        $this->assertSame([$this, "testWhatever", ], $job->callback);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertCount(0, $job->onAfterExecute);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
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
