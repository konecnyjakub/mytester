<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class IniFileConfigAdapter
 *
 * @author Jakub Konečný
 */
#[TestSuite("IniFileConfigAdapter")]
#[Group("config")]
#[Group("configFile")]
final class IniFileConfigAdapterTest extends TestCase
{
    public function testGetPriority(): void
    {
        $adapter = new IniFileConfigAdapter("");
        $this->assertSame(PHP_INT_MAX - 5, $adapter->getPriority());
    }

    public function testParsingOnlyLocal(): void
    {
        $adapter = new IniFileConfigAdapter(
            __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "local-only"
        );
        $this->assertSame(true, $adapter->getUseColors());
        $this->assertSame(null, $adapter->getIncludePhptTests());
        $this->assertSame(null, $adapter->getPath());
        $this->assertSame([], $adapter->getOnlyGroups());
        $this->assertSame([], $adapter->getExcludedGroups());
        $this->assertSame([], $adapter->getExcludedFolders());
        $this->assertSame([], $adapter->getResultsFormatters());
    }

    public function testParsingOnlyDist(): void
    {
        $adapter = new IniFileConfigAdapter(
            __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "dist-only"
        );
        $this->assertSame(null, $adapter->getUseColors());
        $this->assertSame(true, $adapter->getIncludePhptTests());
        $this->assertSame(null, $adapter->getPath());
        $this->assertSame([], $adapter->getOnlyGroups());
        $this->assertSame([], $adapter->getExcludedGroups());
        $this->assertSame([], $adapter->getExcludedFolders());
        $this->assertSame([], $adapter->getResultsFormatters());
    }

    public function testParsingBoth(): void
    {
        $adapter = new IniFileConfigAdapter(
            __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "both"
        );
        $this->assertSame(null, $adapter->getUseColors());
        $this->assertSame(true, $adapter->getIncludePhptTests());
        $this->assertSame(null, $adapter->getPath());
        $this->assertSame([], $adapter->getOnlyGroups());
        $this->assertSame([], $adapter->getExcludedGroups());
        $this->assertSame([], $adapter->getExcludedFolders());
        $this->assertSame([], $adapter->getResultsFormatters());
    }
}
