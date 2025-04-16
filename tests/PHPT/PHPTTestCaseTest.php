<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use Konecnyjakub\EventDispatcher\DebugEventDispatcher;
use Konecnyjakub\EventDispatcher\DummyEventDispatcher;
use Konecnyjakub\PHPTRunner\Parser;
use Konecnyjakub\PHPTRunner\PhpRunner;
use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\AssertionFailedException;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\Events;
use MyTester\Job;
use MyTester\SkippedTestException;
use MyTester\TestCase;
use MyTester\TestsFolderProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\NullLogger;
use ReflectionMethod;

/**
 * Test suite for class PHPTTestCase
 *
 * @author Jakub Konečný
 */
#[TestSuite("PHPTTestCase")]
#[Group("phpt")]
final class PHPTTestCaseTest extends TestCase
{
    public function testGetSuiteName(): void
    {
        $testCase = $this->createTestCase(__DIR__);
        $this->assertSame("PHPT files", $testCase->getSuiteName());
    }

    public function testGetJobs(): void
    {
        $testCase = $this->createTestCase(__DIR__ . "/../../tests_phpt");
        $rm = new ReflectionMethod($testCase, "getJobs");
        /** @var Job[] $result */
        $result = $rm->invoke($testCase);
        $this->assertArrayOfType(Job::class, $result);
        $this->assertCount(4, $result);
        $this->assertSame("Failing test", $result[0]->name);
        $this->assertSame("Skipped test", $result[1]->name);
        $this->assertSame("Test", $result[2]->name);
        $this->assertSame("Test env", $result[3]->name);
    }

    public function testRunFile(): void
    {
        $this->assertThrowsException(function () {
            $testCase = $this->createTestCase(__DIR__ . "/../../tests_phpt");
            $testCase->runFile(__DIR__ . DIRECTORY_SEPARATOR . "/../../tests_phpt/skipped_test.phpt");
        }, SkippedTestException::class, "skip");
        $this->assertOutput(function () {
            $testCase = $this->createTestCase(__DIR__ . "/../../tests_phpt");
            $testCase->runFile(__DIR__ . DIRECTORY_SEPARATOR . "/../../tests_phpt/test.phpt");
        }, "");
        $this->assertOutput(function () {
            $testCase = $this->createTestCase(__DIR__ . "/../../tests_phpt");
            $testCase->runFile(__DIR__ . DIRECTORY_SEPARATOR . "/../../tests_phpt/test_env.phpt");
        }, "");
        $this->assertThrowsException(function () {
            $testCase = $this->createTestCase(__DIR__ . "/../../tests_phpt");
            $testCase->runFile(__DIR__ . DIRECTORY_SEPARATOR . "/../../tests_phpt/failing_test.phpt");
        }, AssertionFailedException::class, "Test 1 failed. Output is not 'test1234' but 'test123'.");
    }

    public function testRun(): void
    {
        $eventDispatcher = new DebugEventDispatcher(new DummyEventDispatcher(), new NullLogger());
        $testCase = $this->createTestCase(__DIR__ . "/../../tests_phpt", $eventDispatcher);
        $testCase->run();
        $this->assertTrue($eventDispatcher->dispatched(Events\TestStarted::class, 4));
        $this->assertFalse($eventDispatcher->dispatched(Events\TestStarted::class, 5));
        $this->assertTrue($eventDispatcher->dispatched(Events\TestPassed::class, 2));
        $this->assertFalse($eventDispatcher->dispatched(Events\TestPassed::class, 3));
        $this->assertTrue($eventDispatcher->dispatched(Events\TestFailed::class, 1));
        $this->assertFalse($eventDispatcher->dispatched(Events\TestFailed::class, 2));
        $this->assertTrue($eventDispatcher->dispatched(Events\TestSkipped::class, 1));
        $this->assertFalse($eventDispatcher->dispatched(Events\TestSkipped::class, 2));
    }

    private function createTestCase(
        string $folder,
        EventDispatcherInterface $eventDispatcher = new DummyEventDispatcher()
    ): PHPTTestCase {
        $testCase = new PHPTTestCase(
            new PhptRunner(new Parser(), new PhpRunner()),
            new TestsFolderProvider($folder)
        );
        $testCase->setEventDispatcher($eventDispatcher);
        return $testCase;
    }
}
