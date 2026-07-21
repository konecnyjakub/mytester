<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class NeonFileConfigAdapter
 *
 * @author Jakub Konečný
 */
#[TestSuite("NeonFileConfigAdapter")]
#[Group("config")]
#[Group("configFile")]
#[Group("nette")]
final class NeonFileConfigAdapterTest extends TestCase
{
    public function testGetPriority(): void
    {
        $adapter = new NeonFileConfigAdapter("");
        $this->assertSame(PHP_INT_MAX - 1, $adapter->getPriority());
    }

    public function testParsingOnlyLocal(): void
    {
        $adapter = new NeonFileConfigAdapter(
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
        $adapter = new NeonFileConfigAdapter(
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
        $adapter = new NeonFileConfigAdapter(
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
