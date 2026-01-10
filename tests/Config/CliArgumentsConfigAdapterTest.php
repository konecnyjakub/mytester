<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\ResultsFormatters\Console;
use MyTester\ResultsFormatters\JUnit;
use MyTester\TestCase;
use ValueError;

/**
 * Test suite for class CliArgumentsConfigAdapter
 *
 * @author Jakub Konečný
 */
#[TestSuite("CliArgumentsConfigAdapter")]
#[Group("config")]
final class CliArgumentsConfigAdapterTest extends TestCase
{
    public function testEmptyConfig(): void
    {
        $configAdapter = new CliArgumentsConfigAdapter([
            CliArgumentsConfigAdapter::ARGUMENT_RESULTS => [],
            CliArgumentsConfigAdapter::ARGUMENT_FILTER_ONLY_GROUPS => "",
            CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_GROUPS => "",
            CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_FOLDERS => "",
        ]);
        $this->assertSame(PHP_INT_MAX, $configAdapter->getPriority());
        $this->assertNull($configAdapter->getUseColors());
        $this->assertNull($configAdapter->getIncludePhptTests());
        $this->assertNull($configAdapter->getPath());
        $this->assertSame([], $configAdapter->getResultsFormatters());
        $this->assertSame([], $configAdapter->getOnlyGroups());
        $this->assertSame([], $configAdapter->getExcludedGroups());
        $this->assertSame([], $configAdapter->getExcludedFolders());
    }

    public function testConfigValues(): void
    {
        $configAdapter = new CliArgumentsConfigAdapter([
            CliArgumentsConfigAdapter::ARGUMENT_RESULTS => ["console", "junit:test.xml",],
            CliArgumentsConfigAdapter::ARGUMENT_FILTER_ONLY_GROUPS => "one,two",
            CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_GROUPS => "three,four",
            CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_FOLDERS => "five",
            CliArgumentsConfigAdapter::ARGUMENT_COLORS => true,
            CliArgumentsConfigAdapter::ARGUMENT_NO_PHPT => true,
            CliArgumentsConfigAdapter::ARGUMENT_PATH => "abc",
        ]);
        $this->assertSame(PHP_INT_MAX, $configAdapter->getPriority());
        $this->assertSame(true, $configAdapter->getUseColors());
        $this->assertSame(false, $configAdapter->getIncludePhptTests());
        $this->assertSame("abc", $configAdapter->getPath());
        $this->assertSame(["one", "two",], $configAdapter->getOnlyGroups());
        $this->assertSame(["three", "four",], $configAdapter->getExcludedGroups());
        $this->assertSame(["five",], $configAdapter->getExcludedFolders());
        $resultsFormatters = $configAdapter->getResultsFormatters();
        $this->assertType("array", $resultsFormatters);
        $this->assertCount(2, $resultsFormatters);
        $this->assertType(Console::class, $resultsFormatters[0]);
        $this->assertType(JUnit::class, $resultsFormatters[1]);
        $this->assertSame("path/test.xml", $resultsFormatters[1]->getOutputFileName("path"));
    }

    public function testInvalidConfig(): void
    {
        $this->assertThrowsException(function () {
            $configAdapter = new CliArgumentsConfigAdapter([
                CliArgumentsConfigAdapter::ARGUMENT_RESULTS => ["test", "junit:test.xml",],
                CliArgumentsConfigAdapter::ARGUMENT_FILTER_ONLY_GROUPS => "one,two",
                CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_GROUPS => "three,four",
                CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_FOLDERS => "five",
                CliArgumentsConfigAdapter::ARGUMENT_COLORS => true,
                CliArgumentsConfigAdapter::ARGUMENT_NO_PHPT => true,
                CliArgumentsConfigAdapter::ARGUMENT_PATH => "abc",
            ]);
            $configAdapter->getResultsFormatters();
        }, ValueError::class, "Unknown results formatter test");
    }
}
