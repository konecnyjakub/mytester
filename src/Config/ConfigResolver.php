<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\ResultsFormatter;
use MyTester\TestsFolderProvider;
use MyTester\TestSuitesSelectionCriteria;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ConfigResolver
{
    /**
     * @var ConfigAdapter[]
     */
    private array $adapters = [];

    public function addAdapter(ConfigAdapter $adapter): void
    {
        $this->adapters[] = $adapter;
    }

    /**
     * @return ConfigAdapter[]
     */
    private function getAdapters(): array
    {
        $adapters = $this->adapters;
        usort($adapters, fn(ConfigAdapter $a, ConfigAdapter $b): int => $b->getPriority() <=> $a->getPriority());
        return $adapters;
    }

    public function getTestsFolderProvider(): TestsFolderProvider
    {
        foreach ($this->getAdapters() as $adapter) {
            $value = $adapter->getPath();
            if ($value !== null) {
                break;
            }
        }

        return new TestsFolderProvider($value ?? getcwd() . DIRECTORY_SEPARATOR . "tests");
    }

    public function getTestSuitesSelectionCriteria(): TestSuitesSelectionCriteria
    {
        $onlyGroups = $exceptGroups = $exceptFolders = [];

        foreach ($this->getAdapters() as $adapter) {
            $resolverOnlyGroups = $adapter->getOnlyGroups();
            if (count($onlyGroups) === 0 && count($resolverOnlyGroups) > 0) {
                $onlyGroups = $resolverOnlyGroups;
            }

            $resolverExceptGroups = $adapter->getExcludedGroups();
            if (count($exceptGroups) === 0 && count($resolverExceptGroups) > 0) {
                $exceptGroups = $resolverExceptGroups;
            }

            $resolverExceptFolders = $adapter->getExcludedFolders();
            if (count($exceptFolders) === 0 && count($resolverExceptFolders) > 0) {
                $exceptFolders = $resolverExceptFolders;
            }
        }

        return new TestSuitesSelectionCriteria(
            $this->getTestsFolderProvider(),
            onlyGroups: $onlyGroups,
            exceptGroups: $exceptGroups,
            exceptFolders: $exceptFolders
        );
    }

    public function getIncludePhptTests(): bool
    {
        foreach ($this->getAdapters() as $adapter) {
            $value = $adapter->getIncludePhptTests();
            if ($value !== null) {
                return $value;
            }
        }

        return true;
    }

    public function getUseColors(): bool
    {
        foreach ($this->getAdapters() as $adapter) {
            $value = $adapter->getUseColors();
            if ($value !== null) {
                return $value;
            }
        }

        return false;
    }

    /**
     * @return ResultsFormatter[]
     */
    public function getResultsFormatters(): array
    {
        foreach ($this->getAdapters() as $adapter) {
            $value = $adapter->getResultsFormatters();
            if (count($value) > 0) {
                return $value;
            }
        }
        return [];
    }
}
