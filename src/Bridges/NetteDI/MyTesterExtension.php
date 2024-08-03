<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Exception;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\CodeCoverage\CodeCoverageExtension;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\CodeCoverage\Formatters\PercentFormatter;
use MyTester\ITesterExtension;
use MyTester\Tester;
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
    public const TAG_TEST = "mytester.test";
    public const TAG_EXTENSION = "mytester.extension";
    public const TAG_COVERAGE_ENGINE = "mytester.coverage.engine";
    public const TAG_COVERAGE_FORMATTER = "mytester.coverage.formatter";
    private const SERVICE_RUNNER = "runner";
    private const SERVICE_SUITE_FACTORY = "suiteFactory";
    private const SERVICE_EXTENSION_PREFIX = "extension.";
    private const SERVICE_CC_COLLECTOR = "coverage.collector";
    private const SERVICE_CC_ENGINE_PREFIX = "coverage.engine.";
    private const SERVICE_CC_FORMATTER_PREFIX = "coverage.formatter";

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
        ])->castTo("array");
    }

    /**
     * @throws Exception
     */
    public function loadConfiguration(): void
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix(static::SERVICE_RUNNER))
            ->setFactory(Tester::class, [$config["folder"]]);

        $builder->addDefinition($this->prefix(static::SERVICE_SUITE_FACTORY))
            ->setType(ContainerSuiteFactory::class);

        $extensions = array_merge([CodeCoverageExtension::class, ], $config["extensions"]);
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
    }

    public function afterCompile(\Nette\PhpGenerator\ClassType $class): void
    {
        $config = $this->getConfig();
        $this->initialization->addBody('$runner = $this->getService(?);', [$this->prefix(static::SERVICE_RUNNER)]);
        $this->initialization->addBody('$runner->useColors = ?;', [$config["colors"]]);
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
