<?php
declare(strict_types=1);

namespace MyTester\Config;

use MyTester\ResultsFormatters\Helper;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use ValueError;

/**
 * @author Jakub Konečný
 * @internal
 */
abstract class BaseFileConfigAdapter implements ConfigAdapter
{
    public const string KEY_COLORS = "colors";
    public const string KEY_INCLUDE_PHPT_TESTS = "phptTests";
    public const string KEY_PATH = "folder";
    public const string KEY_ONLY_GROUPS = "filterOnlyGroups";
    public const string KEY_EXCLUDED_GROUPS = "filterExceptGroups";
    public const string KEY_EXCLUDED_FOLDERS = "filterExceptFolders";
    public const string KEY_RESULTS_FORMATTERS = "resultsFormatters";
    public const string KEY_BOOTSTRAP_FILE = "bootstrap";

    /** @var array{useColors?: bool, includePhptTests?: bool, path?: string, onlyGroups?: list<string>, excludedGroups?: list<string>, excludedFolders?: list<string>, resultsFormatters?: list<string>, bootstrap?: string} */
    protected array $config = [];

    public function __construct(public readonly string $basePath)
    {
    }

    /**
     * @return array<string, mixed>
     */
    abstract protected function parseConfig(): array;

    abstract public function isAvailable(): bool;

    protected function resolveConfig(): void
    {
        if (count($this->config) > 0) {
            return;
        }

        $config = $this->parseConfig();
        if (count($config) === 0) {
            return;
        }

        $schema = Expect::structure([
            self::KEY_COLORS => Expect::anyOf(Expect::bool(), Expect::null()),
            self::KEY_INCLUDE_PHPT_TESTS => Expect::anyOf(Expect::bool(), Expect::null()),
            self::KEY_PATH => Expect::anyOf(Expect::string(), Expect::null()),
            self::KEY_ONLY_GROUPS => Expect::listOf("string")->default([]),
            self::KEY_EXCLUDED_GROUPS => Expect::listOf("string")->default([]),
            self::KEY_EXCLUDED_FOLDERS => Expect::listOf("string")->default([]),
            self::KEY_RESULTS_FORMATTERS => Expect::listOf("string")->default([]),
            self::KEY_BOOTSTRAP_FILE => Expect::anyOf(Expect::string(), Expect::null()),
        ])->castTo("array");
        $processor = new Processor();
        /** @var array{colors?: bool, phptTests?: bool, folder?: string, filterOnlyGroups?: list<string>, filterExceptGroups?: list<string>, filterExceptFolders?: list<string>, resultsFormatters?: list<string>, bootstrap?: string} $config */
        $config = $processor->process($schema, $config);

        if (array_key_exists(self::KEY_COLORS, $config)) {
            $this->config["useColors"] = $config[self::KEY_COLORS];
        }
        if (array_key_exists(self::KEY_INCLUDE_PHPT_TESTS, $config)) {
            $this->config["includePhptTests"] = $config[self::KEY_INCLUDE_PHPT_TESTS];
        }
        if (array_key_exists(self::KEY_PATH, $config)) {
            $this->config["path"] = $config[self::KEY_PATH];
        }
        if (array_key_exists(self::KEY_ONLY_GROUPS, $config)) {
            $this->config["onlyGroups"] = $config[self::KEY_ONLY_GROUPS];
        }
        if (array_key_exists(self::KEY_EXCLUDED_GROUPS, $config) && count($config[self::KEY_EXCLUDED_GROUPS]) > 0) {
            $this->config["excludedGroups"] = $config[self::KEY_EXCLUDED_GROUPS];
        }
        if (array_key_exists(self::KEY_EXCLUDED_FOLDERS, $config) && count($config[self::KEY_EXCLUDED_FOLDERS]) > 0) {
            $this->config["excludedFolders"] = $config[self::KEY_EXCLUDED_FOLDERS];
        }
        if (array_key_exists(self::KEY_RESULTS_FORMATTERS, $config) && count($config[self::KEY_RESULTS_FORMATTERS]) > 0) {
            $this->config["resultsFormatters"] = $config[self::KEY_RESULTS_FORMATTERS];
        }
        if (array_key_exists(self::KEY_BOOTSTRAP_FILE, $config) && $config[self::KEY_BOOTSTRAP_FILE] !== "") {
            $this->config["bootstrap"] = $config[self::KEY_BOOTSTRAP_FILE];
        }
    }

    /**
     * @return list<string>
     */
    protected function getFileNames(string $extension): array
    {

        return [
            $this->basePath . DIRECTORY_SEPARATOR . "mytester." . $extension,
            $this->basePath . DIRECTORY_SEPARATOR . "mytester.dist." . $extension,
        ];
    }

    public function getUseColors(): ?bool
    {
        $this->resolveConfig();
        return $this->config["useColors"] ?? null;
    }

    public function getIncludePhptTests(): ?bool
    {
        $this->resolveConfig();
        return $this->config["includePhptTests"] ?? null;
    }

    public function getPath(): ?string
    {
        $this->resolveConfig();
        return $this->config["path"] ?? null;
    }

    public function getOnlyGroups(): array
    {
        $this->resolveConfig();
        return $this->config["onlyGroups"] ?? [];
    }

    public function getExcludedGroups(): array
    {
        $this->resolveConfig();
        return $this->config["excludedGroups"] ?? [];
    }

    public function getExcludedFolders(): array
    {
        $this->resolveConfig();
        return $this->config["excludedFolders"] ?? [];
    }

    public function getResultsFormatters(): array
    {
        $this->resolveConfig();
        if (!array_key_exists("resultsFormatters", $this->config) || count($this->config["resultsFormatters"]) === 0) {
            return [];
        }
        $result = [];
        foreach ($this->config["resultsFormatters"] as $formatter) {
            if (array_key_exists($formatter, Helper::$availableFormatters)) {
                $result[] = new Helper::$availableFormatters[$formatter]();
            } else {
                throw new ValueError("Unknown results formatter " . $formatter);
            }
        }
        return $result;
    }

    public function getBootstrapFile(): ?string
    {
        $this->resolveConfig();
        return $this->config["bootstrap"] ?? null;
    }
}
