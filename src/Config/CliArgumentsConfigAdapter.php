<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\ResultsFormatters\Helper as ResultsHelper;
use ValueError;

/**
 * @author Jakub Konečný
 * @internal
 */
final readonly class CliArgumentsConfigAdapter implements ConfigAdapter
{
    public const string ARGUMENT_PATH = "path";
    public const string ARGUMENT_COLORS = "--colors";
    public const string ARGUMENT_NO_PHPT = "--noPhpt";
    public const string ARGUMENT_RESULTS = "--results";
    public const string ARGUMENT_FILTER_ONLY_GROUPS = "--filterOnlyGroups";
    public const string ARGUMENT_FILTER_EXCEPT_GROUPS = "--filterExceptGroups";
    public const string ARGUMENT_FILTER_EXCEPT_FOLDERS = "--filterExceptFolders";

    /**
     * @param array{path?: string, "--colors"?: bool, "--results": string[], "--filterOnlyGroups": string, "--filterExceptGroups": string,"--filterExceptFolders": string, "--noPhpt"?: bool} $parsedOptions
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

    public function getResultsFormatters(): array
    {
        $resultsFormatters = [];
        foreach ($this->parsedOptions[self::ARGUMENT_RESULTS] as $results) {
            $results = explode(":", $results, 2);
            if (!array_key_exists($results[0], ResultsHelper::$availableFormatters)) {
                throw new ValueError("Unknown results formatter " . $results[0]);
            }
            /** @var \MyTester\ResultsFormatter $resultsFormatter */
            $resultsFormatter = new ResultsHelper::$availableFormatters[$results[0]]();
            if (isset($results[1])) {
                $resultsFormatter->setOutputFileName($results[1]);
            }
            $resultsFormatters[] = $resultsFormatter;
        }
        return $resultsFormatters;
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
