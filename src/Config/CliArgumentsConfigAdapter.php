<?php
declare(strict_types=1);

namespace MyTester\Config;

/**
 * @author Jakub Konečný
 * @internal
 */
final readonly class CliArgumentsConfigAdapter implements ConfigAdapter
{
    public const string ARGUMENT_PATH = "path";
    public const string ARGUMENT_COLORS = "--colors";
    public const string ARGUMENT_NO_PHPT = "--noPhpt";
    public const string ARGUMENT_FILTER_ONLY_GROUPS = "--filterOnlyGroups";
    public const string ARGUMENT_FILTER_EXCEPT_GROUPS = "--filterExceptGroups";
    public const string ARGUMENT_FILTER_EXCEPT_FOLDERS = "--filterExceptFolders";

    /**
     * @param array{path?: string, "--colors"?: bool, "--filterOnlyGroups": string, "--filterExceptGroups": string,"--filterExceptFolders": string, "--noPhpt"?: bool} $parsedOptions
     */
    public function __construct(private array $parsedOptions)
    {
    }

    public function getPriority(): int
    {
        return PHP_INT_MAX;
    }

    public function getUseColors(): ?bool
    {
        return isset($this->parsedOptions[self::ARGUMENT_COLORS]) ? true : null;
    }

    public function getIncludePhptTests(): ?bool
    {
        return isset($this->parsedOptions[self::ARGUMENT_NO_PHPT]) ? false : null;
    }

    public function getPath(): ?string
    {
        return $this->parsedOptions[self::ARGUMENT_PATH] ?? null;
    }

    public function getOnlyGroups(): array
    {
        return $this->getArrayFromList($this->parsedOptions[self::ARGUMENT_FILTER_ONLY_GROUPS]);
    }

    public function getExcludedGroups(): array
    {
        return $this->getArrayFromList($this->parsedOptions[self::ARGUMENT_FILTER_EXCEPT_GROUPS]);
    }

    public function getExcludedFolders(): array
    {
        return $this->getArrayFromList($this->parsedOptions[self::ARGUMENT_FILTER_EXCEPT_FOLDERS]);
    }

    /**
     * @return string[]
     */
    private function getArrayFromList(string $value): array
    {
        if ($value === "") {
            return [];
        }
        if (!str_contains($value, ",")) {
            return [$value];
        }
        return explode(",", $value);
    }
}
