<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class SkippedTest
 *
 * @author Jakub Konečný
 */
#[TestSuite("SkippedTestTest")]
final class SkippedTestTest extends TestCase
{
    public function testToString(): void
    {
        $skippedTest = new SkippedTest("Test 1", "");
        $this->assertSame("Skipped Test 1\n", (string) $skippedTest);

        $skippedTest = new SkippedTest("Test 2", "Reason");
        $this->assertSame("Skipped Test 2: Reason\n", (string) $skippedTest);
    }
}
