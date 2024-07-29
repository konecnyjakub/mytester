<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class TestWarning
 *
 * @author Jakub Konečný
 */
#[TestSuite("TestWarningTest")]
final class TestWarningTest extends TestCase
{
    public function testToString(): void
    {
        $testWarning = new TestWarning("Test 1", "Text");
        $this->assertSame("Test 1 passed with warning: Text", (string) $testWarning);
    }
}
