<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class Helper
 *
 * @author Jakub Konečný
 */
#[TestSuite("Results formatter helper")]
final class HelperTest extends TestCase
{
    public function testIsFileOutput(): void
    {
        $this->assertFalse(Helper::isFileOutput("php://stdout"));
        $this->assertFalse(Helper::isFileOutput("php://stderr"));
        $this->assertFalse(Helper::isFileOutput("php://output"));
        $this->assertTrue(Helper::isFileOutput(__FILE__));
        $this->assertTrue(Helper::isFileOutput("/var/project/non_existing.txt"));
    }
}
