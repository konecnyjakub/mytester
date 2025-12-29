<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\TesterExtension;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Config
{
    public string $folder;
    /** @var class-string<TesterExtension>[] */
    public array $extensions = [];
    public bool $colors = false;
    public ?string $coverageFormat = null;
    public string $resultsFormat;
    /** @var string[] */
    public array $filterOnlyGroups = [];
    /** @var string[] */
    public array $filterExceptGroups = [];
    /** @var string[] */
    public array $filterExceptFolders = [];
}
