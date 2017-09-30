<?php
namespace MyTester\Bridges\NetteDI;

/**
 * MyTester Extension for Nette DIC
 *
 * @author Jakub Konečný
 * @copyright (c) 2016-2017, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
class MyTesterExtension extends \Nette\DI\CompilerExtension {
  const TAG = "mytester.test";
  
  /** @var array */
  private $suits;
  /** @var array */
  protected $defaults = ["folder" => "%appDir%/../tests", "onExecute" => []];
  
  /**
   * @return void
   * @throws \Exception
   */
  public function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("runner"))
      ->setClass(TestsRunner::class);
    if(!is_dir($config["folder"])) {
      throw new \Exception("Invalid folder {$config["folder"]} for $this->name.folder");
    }
    $tester = new \MyTester\Tester($config["folder"]);
    $this->suits = $tester->getSuits();
    foreach($this->suits as $index => $suit) {
      $builder->addDefinition($this->prefix("test." . ($index + 1)))
        ->setClass($suit[0])
        ->addTag(self::TAG);
    }
  }
  
  /**
   * @param \Nette\PhpGenerator\ClassType $class
   * @return void
   */
  public function afterCompile(\Nette\PhpGenerator\ClassType $class) {
    $config = $this->getConfig($this->defaults);
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