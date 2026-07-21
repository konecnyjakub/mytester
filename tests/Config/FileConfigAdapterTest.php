<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;

/**
 * Test suite for class FileConfigAdapter
 *
 * @author Jakub Konečný
 */
#[TestSuite("FileConfigAdapter")]
#[Group("config")]
#[Group("configFile")]
final class FileConfigAdapterTest extends TestCase
{
    public function testGetPriority(): void
    {
        $adapter = new FileConfigAdapter("");
        $this->assertSame(PHP_INT_MAX - 1, $adapter->getPriority());
    }

    public function testParsing(): void
    {
        $adapter = new FileConfigAdapter(__DIR__ . DIRECTORY_SEPARATOR . "config");
        $this->assertSame(true, $adapter->getUseColors());
        $this->assertSame(true, $adapter->getIncludePhptTests());
        $this->assertSame(null, $adapter->getPath());
        $this->assertSame([], $adapter->getOnlyGroups());
        $this->assertSame([], $adapter->getExcludedGroups());
        $this->assertSame([], $adapter->getExcludedFolders());
        $this->assertSame([], $adapter->getResultsFormatters());
    }
}
