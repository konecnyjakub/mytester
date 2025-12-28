<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventDispatcher;
use MyTester\Attributes\AfterTest;
use MyTester\Attributes\AfterTestSuite;
use MyTester\Attributes\BeforeTest;
use MyTester\Attributes\Data;
use MyTester\Attributes\DataProvider as DataProviderAttribute;
use MyTester\Attributes\DataProviderExternal;
use MyTester\Attributes\FlakyTest;
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
use ReflectionFunction;
use ReflectionMethod;

/**
 * Test suite for class TestCase
 *
 * @author Jakub Konečný
 */
#[TestSuite("TestCase")]
final class TestCaseTest extends TestCase
{
    private int $one = 0;
    private int $flakyTest = 0;

    #[BeforeTest]
    public function setUp(): void
    {
        $this->one++;
    }

    #[AfterTest]
    public function tearDown(): void
    {
        $this->one = 0;
    }

    #[AfterTestSuite]
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
                "testGetMaxRetries",
                "testFlakyTest",
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

    public function testGetMaxRetries(): void
    {
        $this->assertSame(0, $this->getMaxRetries(__FUNCTION__));
    }

    #[FlakyTest(1)]
    public function testFlakyTest(): void
    {
        $this->flakyTest++;
        $this->assertSame(2, $this->flakyTest);
    }

    public function testGetJobs(): void
    {
        $jobs = $this->getJobs();
        $this->assertCount(34, $jobs);

        $job = $jobs[0];
        $this->assertSame("TestCase::testState", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testState", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[1];
        $this->assertSame("TestCase::testParams", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParams", $rm->name);
        $this->assertSame(["abc", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[2];
        $this->assertSame("TestCase::testParams", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParams", $rm->name);
        $this->assertSame(["adef", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[3];
        $this->assertSame("TestCase::testParamsNoneProvided", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsNoneProvided", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertSame("Method requires at least 1 parameter but data provider does not provide any.", $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[4];
        $this->assertSame("TestCase::testParamsNotEnough", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsNotEnough", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertSame("Method requires at least 2 parameter(s) but data provider provides only 1.", $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[5];
        $this->assertSame("TestCase::testParamsMulti", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsMulti", $rm->name);
        $this->assertSame(["abc", 1, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("first", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[6];
        $this->assertSame("TestCase::testParamsMulti", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsMulti", $rm->name);
        $this->assertSame(["abcd", 2, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[7];
        $this->assertSame("TestCase::testParamsIterator", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsIterator", $rm->name);
        $this->assertSame([1, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("first", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[8];
        $this->assertSame("TestCase::testParamsIterator", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsIterator", $rm->name);
        $this->assertSame([2, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[9];
        $this->assertSame("TestCase::testParamsExternal", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsExternal", $rm->name);
        $this->assertSame(["abc", 1, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("first", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[10];
        $this->assertSame("TestCase::testParamsExternal", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsExternal", $rm->name);
        $this->assertSame(["abcd", 2, ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[11];
        $this->assertSame("TestCase::testParamsData", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsData", $rm->name);
        $this->assertSame(["abc", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[12];
        $this->assertSame("TestCase::testParamsData", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testParamsData", $rm->name);
        $this->assertSame(["abcd", ], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[13];
        $this->assertSame("Custom name", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testTestName", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[14];
        $this->assertSame("Skip", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testSkip", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[15];
        $this->assertSame("PHP version", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testSkipPhpVersion", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[16];
        $this->assertSame("CGI sapi", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testCgiSapi", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[17];
        $this->assertSame("Extension", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testSkipExtension", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[18];
        $this->assertSame("OS family", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testSkipOsFamily", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[19];
        $this->assertSame("Package", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testSkipPackage", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[20];
        $this->assertSame("Env variable", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testSkipEnvVariable", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertTrue((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[21];
        $this->assertSame("No assertions", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testNoAssertions", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[22];
        $this->assertSame("TestCase::testShouldCheckAssertions", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testShouldCheckAssertions", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[23];
        $this->assertSame("Deprecation", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testDeprecation", $rm->name);
        $this->assertSame([], $job->params);
        if (version_compare(PHP_VERSION, "8.4.0") >= 0) {
            $this->assertFalse((bool) $job->skip);
        } else {
            $this->assertSame("PHP >=8.4 is required", $job->skip);
        }
        $this->assertSame("", $job->dataSetName);
        $this->assertFalse($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[24];
        $this->assertSame("TestCase::testGetSuiteName", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testGetSuiteName", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[25];
        $this->assertSame("TestCase::testGetJobName", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testGetJobName", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[26];
        $this->assertSame("TestCase::testGetTestMethodsNames", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testGetTestMethodsNames", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[27];
        $this->assertSame("TestCase::testShouldReportDeprecations", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testShouldReportDeprecations", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[28];
        $this->assertSame("TestCase::testGetMaxRetries", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testGetMaxRetries", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[29];
        $this->assertSame("TestCase::testFlakyTest", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testFlakyTest", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(1, $job->maxRetries);

        $job = $jobs[30];
        $this->assertSame("TestCase::testGetJobs", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testGetJobs", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[31];
        $this->assertSame("TestCase::testIncomplete", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testIncomplete", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[32];
        $this->assertSame("TestCase::testSkipInside", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testSkipInside", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);

        $job = $jobs[33];
        $this->assertSame("TestCase::testWhatever", $job->name);
        /** @var ReflectionFunction $rm */
        $rm = $job->getCallbackReflection();
        $this->assertType(ReflectionFunction::class, $rm);
        $this->assertType(self::class, $rm->getClosureThis());
        $this->assertSame("testWhatever", $rm->name);
        $this->assertSame([], $job->params);
        $this->assertFalse((bool) $job->skip);
        $this->assertSame("", $job->dataSetName);
        $this->assertTrue($job->reportDeprecations);
        $this->assertSame(0, $job->maxRetries);
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
