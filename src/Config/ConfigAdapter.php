<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\ResultsFormatter;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ConfigAdapter
{
    public function getPriority(): int;
    public function getUseColors(): ?bool;
    public function getIncludePhptTests(): ?bool;
    public function getPath(): ?string;

    /**
     * @return string[]
     */
    public function getOnlyGroups(): array;

    /**
     * @return string[]
     */
    public function getExcludedGroups(): array;

    /**
     * @return string[]
     */
    public function getExcludedFolders(): array;

    /**
     * @return ResultsFormatter[]
     */
    public function getResultsFormatters(): array;
}
