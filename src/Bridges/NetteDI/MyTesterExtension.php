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
final class MyTesterExtension extends \Nette\DI\CompilerExtension {
  public const TAG = "mytester.test";

  private array $suites;

  public function getConfigSchema(): \Nette\Schema\Schema {
    $params = $this->getContainerBuilder()->parameters;
    return Expect::structure([
      "folder" => Expect::string(Helpers::expand("%appDir%/../tests", $params)),
      "onExecute" => Expect::array()->default([]),
    ])->castTo("array");
  }

  /**
   * @throws \Exception
   */
  public function loadConfiguration(): void {
    $config = $this->getConfig();
    $builder = $this->getContainerBuilder();
    if(!is_dir($config["folder"])) {
      throw new Exception("Invalid folder {$config["folder"]} for $this->name.folder");
    }
    $builder->addDefinition($this->prefix("runner"))
      ->setFactory(Tester::class, [$config["folder"]]);
    $builder->addDefinition($this->prefix("suiteFactory"))
      ->setType(ContainerSuiteFactory::class);
    $this->suites = (new TestSuitesFinder())->getSuites($config["folder"]);
    foreach($this->suites as $index => $suite) {
      $builder->addDefinition($this->prefix("test." . ($index + 1)))
        ->setType($suite)
        ->addTag(self::TAG);
    }
  }
  
  public function afterCompile(\Nette\PhpGenerator\ClassType $class): void {
    $config = $this->getConfig();
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$runner = $this->getService(?);', [$this->prefix("runner")]);
    $initialize->addBody('$runner->testSuiteFactory = $this->getService(?);', [$this->prefix("suiteFactory")]);
    foreach($config["onExecute"] as &$task) {
      if(!is_array($task)) {
        $task = explode("::", $task);
      } elseif(substr($task[0], 0, 1) === "@") {
        $initialize->addBody('$runner->onExecute[] = [$this->getService(?), ?];', [substr($task[0], 1), $task[1]]);
        continue;
      }
      $initialize->addBody('$runner->onExecute[] = [?, ?];', [$task[0], $task[1]]);
    }
  }
}
?>