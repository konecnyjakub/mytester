<?php

declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Exception;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
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

    public function getConfigSchema(): \Nette\Schema\Schema
    {
        $params = $this->getContainerBuilder()->parameters;
        return Expect::structure([
            "folder" => Expect::string(Helpers::expand("%appDir%/../tests", $params)),
            "onExecute" => Expect::array()->default([]),
            "onFinish" => Expect::array()->default([]),
            "colors" => Expect::bool(false),
        ])->castTo("array");
    }

    /**
     * @throws Exception
     */
    public function loadConfiguration(): void
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();
        if (!is_dir($config["folder"])) {
            throw new Exception("Invalid folder {$config["folder"]} for $this->name.folder");
        }
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
    }

    public function afterCompile(\Nette\PhpGenerator\ClassType $class): void
    {
        $config = $this->getConfig();
        $initialize = $class->methods["initialize"];
        $initialize->addBody('$runner = $this->getService(?);', [$this->prefix(static::SERVICE_RUNNER)]);
        $initialize->addBody('$runner->useColors = ?;', [$config["colors"]]);
        foreach ($config["onExecute"] as &$task) {
            if (!is_array($task)) {
                $task = explode("::", $task);
            } elseif (str_starts_with($task[0], "@")) {
                $className = substr($task[0], 1);
                $initialize->addBody('$runner->onExecute[] = [$this->getService(?), ?];', [$className, $task[1]]);
                continue;
            }
            $initialize->addBody('$runner->onExecute[] = [?, ?];', [$task[0], $task[1]]);
        }
        foreach ($config["onFinish"] as &$task) {
            if (!is_array($task)) {
                $task = explode("::", $task);
            } elseif (str_starts_with($task[0], "@")) {
                $className = substr($task[0], 1);
                $initialize->addBody('$runner->onFinish[] = [$this->getService(?), ?];', [$className, $task[1]]);
                continue;
            }
            $initialize->addBody('$runner->onFinish[] = [?, ?];', [$task[0], $task[1]]);
        }
    }
}
