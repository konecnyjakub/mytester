<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\ResultsFormatters\Console;
use MyTester\TestCase;

/**
 * Test suite for class ConfigResolver
 *
 * @author Jakub Konečný
 */
#[TestSuite("ConfigResolver")]
#[Group("config")]
final class ConfigResolverTest extends TestCase
{
    public function testNoAdapters(): void
    {
        $config = new ConfigResolver();
        $testsFolderProvider = $config->getTestsFolderProvider();
        $this->assertSame(realpath(__DIR__ . "/../../tests"), realpath($testsFolderProvider->folder));
        $testSuitesSelectionCriteria = $config->getTestSuitesSelectionCriteria();
        $this->assertSame($testsFolderProvider->folder, $testSuitesSelectionCriteria->testsFolderProvider->folder);
        $this->assertSame([], $testSuitesSelectionCriteria->onlyGroups);
        $this->assertSame([], $testSuitesSelectionCriteria->exceptGroups);
        $this->assertSame([], $testSuitesSelectionCriteria->exceptFolders);
        $this->assertTrue($config->getIncludePhptTests());
        $this->assertFalse($config->getUseColors());
    }

    public function testMultipleAdapters(): void
    {
        $config = new ConfigResolver();
        $config->addAdapter(new class implements ConfigAdapter
        {
            public function getPriority(): int
            {
                return 0;
            }

            public function getUseColors(): ?bool
            {
                return true;
            }

            public function getIncludePhptTests(): ?bool
            {
                return true;
            }

            public function getPath(): null
            {
                return null;
            }

            public function getOnlyGroups(): array
            {
                return ["three", "four",];
            }

            public function getExcludedGroups(): array
            {
                return ["one", "two",];
            }

            public function getExcludedFolders(): array
            {
                return ["failing",];
            }

            public function getResultsFormatters(): array
            {
                return [new Console(),];
            }
        });
        $config->addAdapter(new class implements ConfigAdapter
        {
            public function getPriority(): int
            {
                return PHP_INT_MAX;
            }

            public function getUseColors(): ?bool
            {
                return null;
            }

            public function getIncludePhptTests(): ?bool
            {
                return false;
            }

            public function getPath(): string
            {
                return "abc";
            }

            public function getOnlyGroups(): array
            {
                return ["one", "two",];
            }

            public function getExcludedGroups(): array
            {
                return ["three", "four",];
            }

            public function getExcludedFolders(): array
            {
                return [];
            }

            public function getResultsFormatters(): array
            {
                return [];
            }
        });
        $testsFolderProvider = $config->getTestsFolderProvider();
        $this->assertSame("abc", $testsFolderProvider->folder);
        $testSuitesSelectionCriteria = $config->getTestSuitesSelectionCriteria();
        $this->assertSame($testsFolderProvider->folder, $testSuitesSelectionCriteria->testsFolderProvider->folder);
        $this->assertSame(["one", "two",], $testSuitesSelectionCriteria->onlyGroups);
        $this->assertSame(["three", "four",], $testSuitesSelectionCriteria->exceptGroups);
        $this->assertSame(["failing",], $testSuitesSelectionCriteria->exceptFolders);
        $this->assertFalse($config->getIncludePhptTests());
        $this->assertTrue($config->getUseColors());
        $resultsFormatters = $config->getResultsFormatters();
        $this->assertType("array", $resultsFormatters);
        $this->assertCount(1, $resultsFormatters);
        $this->assertType(Console::class, $resultsFormatters[0]);
    }
}
