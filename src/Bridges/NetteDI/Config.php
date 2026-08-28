<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\ITesterExtension;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Config
{
    public string $folder;
    /** @var class-string<ITesterExtension>[] */
    public array $extensions = [];
    public bool $colors = false;
    public ?string $coverageFormat = null;
    /** @var list<string> */
    public array $coverage = [];
    public ?string $resultsFormat = null;
    /** @var list<string> */
    public array $results = [];
    /** @var string[] */
    public array $filterOnlyGroups = [];
    /** @var string[] */
    public array $filterExceptGroups = [];
    /** @var string[] */
    public array $filterExceptFolders = [];
    public string $url = "";
}
