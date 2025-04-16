<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use Konecnyjakub\PHPTRunner\Parser;
use Konecnyjakub\PHPTRunner\PhpRunner;
use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\AssertionFailedException;
use MyTester\Attributes\TestSuite;
use MyTester\SkippedTestException;
use MyTester\TestCase;

/**
 * Test suite for class PHPTTestCase
 *
 * @author Jakub Konečný
 */
#[TestSuite("PHPTTestCase")]
final class PHPTTestCaseTest extends TestCase
{
    public function testGetSuiteName(): void
    {
        $testCase = $this->createTestCase(__DIR__ . DIRECTORY_SEPARATOR . "/test.phpt");
        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR . "/test.phpt", $testCase->getSuiteName());
    }

    public function testTestFile(): void
    {
        $this->assertThrowsException(function () {
            $testCase = $this->createTestCase(__DIR__ . DIRECTORY_SEPARATOR . "/skipped_test.phpt");
            $testCase->testFile();
        }, SkippedTestException::class, "skip");
        $this->assertOutput(function () {
            $testCase = $this->createTestCase(__DIR__ . DIRECTORY_SEPARATOR . "/test.phpt");
            $testCase->testFile();
        }, "");
        $this->assertOutput(function () {
            $testCase = $this->createTestCase(__DIR__ . DIRECTORY_SEPARATOR . "/test_env.phpt");
            $testCase->testFile();
        }, "");
        $this->assertThrowsException(function () {
            $testCase = $this->createTestCase(__DIR__ . DIRECTORY_SEPARATOR . "/failing_test.phpt");
            $testCase->testFile();
        }, AssertionFailedException::class, "Test 1 failed. Output is not 'test1234' but 'test123'.");
    }

    private function createTestCase(string $fileName): PHPTTestCase
    {
        $runner = new PhptRunner(new Parser(), new PhpRunner());
        return new class ($runner, $fileName) extends PHPTTestCase
        {
        };
    }
}
