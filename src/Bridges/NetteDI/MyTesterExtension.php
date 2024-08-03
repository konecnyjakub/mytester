<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Exception;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\CodeCoverage\Formatters\PercentFormatter;
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
    public const TAG = "mytester.test";
    private const SERVICE_RUNNER = "runner";
    private const SERVICE_SUITE_FACTORY = "suiteFactory";
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
            "onExecute" => Expect::array()->default([]),
            "onFinish" => Expect::array()->default([]),
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

        $suites = (new TestSuitesFinder())->getSuites($config["folder"]);
        foreach ($suites as $index => $suite) {
            $builder->addDefinition($this->prefix("test." . ($index + 1)))
                ->setType($suite)
                ->addTag(self::TAG);
        }

        $builder->addDefinition($this->prefix(static::SERVICE_CC_COLLECTOR))
            ->setType(Collector::class);
        foreach (CodeCoverageHelper::$defaultEngines as $name => $className) {
            $builder->addDefinition($this->prefix(static::SERVICE_CC_ENGINE_PREFIX . $name))
                ->setType($className);
        }
        $coverageFormat = $config["coverageFormat"];
        if ($coverageFormat !== null) {
            $this->codeCoverageFormatters[$coverageFormat] = CodeCoverageHelper::$availableFormatters[$coverageFormat];
        }
        foreach ($this->codeCoverageFormatters as $name => $className) {
            $builder->addDefinition($this->prefix(static::SERVICE_CC_FORMATTER_PREFIX . $name))
                ->setType($className);
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
        $this->writeRunnerEventHandlers("onExecute", $config["onExecute"]);
        $this->writeRunnerEventHandlers("onFinish", $config["onFinish"]);
    }

    private function writeRunnerEventHandlers(string $eventName, array $callbacks): void
    {
        foreach ($callbacks as &$task) {
            if (!is_array($task)) {
                $task = explode("::", $task);
            } elseif (str_starts_with($task[0], "@")) {
                $className = substr($task[0], 1);
                $this->initialization->addBody(
                    '$runner->' . $eventName . '[] = [$this->getService(?), ?];',
                    [$className, $task[1]]
                );
                continue;
            }
            $this->initialization->addBody('$runner->' . $eventName . '[] = [?, ?];', [$task[0], $task[1]]);
        }
    }
}
