<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

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

  private array $suits;

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
    $builder->addDefinition($this->prefix("runner"))
      ->setType(TestsRunner::class);
    if(!is_dir($config["folder"])) {
      throw new \Exception("Invalid folder {$config["folder"]} for $this->name.folder");
    }
    $tester = new \MyTester\Tester($config["folder"]);
    $this->suits = $tester->suits;
    foreach($this->suits as $index => $suit) {
      $builder->addDefinition($this->prefix("test." . ($index + 1)))
        ->setType($suit[0])
        ->addTag(self::TAG);
    }
  }
  
  public function afterCompile(\Nette\PhpGenerator\ClassType $class): void {
    $config = $this->getConfig();
    $container = $this->getContainerBuilder();
    $initialize = $class->methods["initialize"];
    $initialize->addBody('$runner = $this->getService(?);', [$this->prefix("runner")]);
    $initialize->addBody('spl_autoload_extensions(spl_autoload_extensions() . ",.phpt");
MyTester\Bridges\NetteDI\TestsRunner::$autoloader = ?;
spl_autoload_register(?);', [$this->suits, TestsRunner::class . "::autoload"]);
    foreach($container->findByTag(self::TAG) as $suit => $foo) {
      $initialize->addBody('$runner->addSuit($this->getService(?));', [$suit]);
    }
    $onExecute = array_merge(['MyTester\Environment::setup', 'MyTester\Environment::printInfo'], $config["onExecute"]);
    foreach($onExecute as &$task) {
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