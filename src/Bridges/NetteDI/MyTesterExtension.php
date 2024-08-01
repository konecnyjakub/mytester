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
        $this->initialization->addBody('$runner = $this->getService(?);', [$this->prefix(static::SERVICE_RUNNER)]);
        $this->initialization->addBody('$runner->useColors = ?;', [$config["colors"]]);
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
