<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Exception;
use MyTester\Bridges\NetteApplication\PresenterMock;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\CodeCoverage\CodeCoverageExtension;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\CodeCoverage\Formatters\PercentFormatter;
use MyTester\ErrorsFilesExtension;
use MyTester\ITesterExtension;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
use MyTester\Tester;
use MyTester\TestsFolderProvider;
use Nette\CommandLine\Console;
use Nette\DI\Helpers;
use Nette\Schema\Expect;

/**
 * MyTester Extension for Nette DIC
 *
 * @author Jakub Konečný
 * @method array getConfig()
 */
final class MyTesterExtension extends \Nette\DI\CompilerExtension
{
    public const string TAG_TEST = "mytester.test";
    public const string TAG_EXTENSION = "mytester.extension";
    public const string TAG_COVERAGE_ENGINE = "mytester.coverage.engine";
    public const string TAG_COVERAGE_FORMATTER = "mytester.coverage.formatter";
    private const string SERVICE_RUNNER = "runner";
    private const string SERVICE_SUITE_FACTORY = "suiteFactory";
    private const string SERVICE_RESULTS_FORMATTER = "resultsFormatter";
    private const string SERVICE_EXTENSION_PREFIX = "extension.";
    private const string SERVICE_CC_COLLECTOR = "coverage.collector";
    private const string SERVICE_CC_ENGINE_PREFIX = "coverage.engine.";
    private const string SERVICE_CC_FORMATTER_PREFIX = "coverage.formatter";
    private const string SERVICE_PRESENTER_MOCK = "presenterMock";
    private const string SERVICE_TESTS_FOLDER_PROVIDER = "testsFolderProvider";
    private const string SERVICE_CONSOLE_WRITER = "consoleWriter";

    private array $codeCoverageFormatters = [
        "percent" => PercentFormatter::class,
    ];

    public function getConfigSchema(): \Nette\Schema\Schema
    {
        $params = $this->getContainerBuilder()->parameters;
        return Expect::structure([
            "folder" => Expect::string(Helpers::expand("%appDir%/../tests", $params))
                ->assert("is_dir", "Invalid folder"),
            "extensions" => Expect::arrayOf("class")
                ->default([])
                ->assert(function (string $classname) {
                    return is_subclass_of($classname, ITesterExtension::class);
                }),
            "colors" => Expect::bool(false),
            "coverageFormat" => Expect::anyOf(
                null,
                ...array_keys(CodeCoverageHelper::$availableFormatters)
            )->default(null),
            "resultsFormat" => Expect::anyOf(
                null,
                ...array_keys(ResultsHelper::$availableFormatters)
            )->default(null),
        ])->castTo("array");
    }

    /**
     * @throws Exception
     */
    public function loadConfiguration(): void
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix(static::SERVICE_TESTS_FOLDER_PROVIDER))
            ->setFactory(TestsFolderProvider::class, [$config["folder"]]);

        $builder->addDefinition($this->prefix(static::SERVICE_RUNNER))
            ->setType(Tester::class);

        $builder->addDefinition($this->prefix(static::SERVICE_SUITE_FACTORY))
            ->setType(ContainerSuiteFactory::class);

        $builder->addDefinition($this->prefix(static::SERVICE_PRESENTER_MOCK))
            ->setType(PresenterMock::class)
            ->setAutowired(PresenterMock::class);

        $extensions = array_merge(
            [CodeCoverageExtension::class, ErrorsFilesExtension::class, ],
            $config["extensions"]
        );
        foreach ($extensions as $index => $extension) {
            $builder->addDefinition($this->prefix(static::SERVICE_EXTENSION_PREFIX . ($index + 1)))
                ->setType($extension)
                ->addTag(static::TAG_EXTENSION);
        }

        $suites = (new TestSuitesFinder())->getSuites($config["folder"]);
        foreach ($suites as $index => $suite) {
            $builder->addDefinition($this->prefix("test." . ($index + 1)))
                ->setType($suite)
                ->addTag(self::TAG_TEST);
        }

        $builder->addDefinition($this->prefix(static::SERVICE_CC_COLLECTOR))
            ->setType(Collector::class);
        foreach (CodeCoverageHelper::$defaultEngines as $name => $className) {
            $builder->addDefinition($this->prefix(static::SERVICE_CC_ENGINE_PREFIX . $name))
                ->setType($className)
                ->addTag(static::TAG_COVERAGE_ENGINE);
        }
        $coverageFormat = $config["coverageFormat"];
        if ($coverageFormat !== null) {
            $this->codeCoverageFormatters[$coverageFormat] = CodeCoverageHelper::$availableFormatters[$coverageFormat];
        }
        foreach ($this->codeCoverageFormatters as $name => $className) {
            $builder->addDefinition($this->prefix(static::SERVICE_CC_FORMATTER_PREFIX . $name))
                ->setType($className)
                ->addTag(static::TAG_COVERAGE_FORMATTER);
        }

        if ($config["resultsFormat"] !== null) {
            $builder->addDefinition($this->prefix(self::SERVICE_RESULTS_FORMATTER))
                ->setType(ResultsHelper::$availableFormatters[$config["resultsFormat"]]);
        }

        $builder->addDefinition($this->prefix(static::SERVICE_CONSOLE_WRITER))
            ->setType(Console::class)
            ->addSetup("useColors", [$config["colors"]]);
    }

    public function afterCompile(\Nette\PhpGenerator\ClassType $class): void
    {
        $this->initialization->addBody(
            '$coverageCollector = $this->getService(?);',
            [$this->prefix(static::SERVICE_CC_COLLECTOR)]
        );
        foreach (array_keys(CodeCoverageHelper::$defaultEngines) as $name) {
            $this->initialization->addBody(
                '$coverageCollector->registerEngine($this->getService(?));',
                [$this->prefix(static::SERVICE_CC_ENGINE_PREFIX . $name)]
            );
        }
        foreach (array_keys($this->codeCoverageFormatters) as $name) {
            $this->initialization->addBody(
                '$coverageCollector->registerFormatter($this->getService(?));',
                [$this->prefix(static::SERVICE_CC_FORMATTER_PREFIX . $name)]
            );
        }
    }
}
