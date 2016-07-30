<?php
namespace MyTester\Bridges\NetteDI;

/**
 * MyTester Extension for Nette DIC
 *
 * @author Jakub Konečný
 * @copyright (c) 2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
class MyTesterExtension extends \Nette\DI\CompilerExtension {
  const TAG = "mytester.test";
  
  /** @var array */
  private $suits;
  /** @var array */
  protected $defaults = ["folder" => "%appDir%/../tests"];
  
  /**
   * @return void
   * @throws \Exception
   */
  function loadConfiguration() {
    $config = $this->getConfig($this->defaults);
    $builder = $this->getContainerBuilder();
    $builder->addDefinition($this->prefix("runner"))
      ->setClass("MyTester\Bridges\NetteDI\TestsRunner");
    if(!is_dir($config["folder"])) throw new \Exception("Invalid folder {$config["folder"]} for $this->name.folder");
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
  function afterCompile(\Nette\PhpGenerator\ClassType $class) {
    $container = $this->getContainerBuilder();
    $initialize = $class->methods["initialize"];
    $initialize->addBody('MyTester\Environment::setup();');
    $initialize->addBody('$runner = $this->getService(?);', [$this->prefix("runner")]);
    $initialize->addBody('spl_autoload_extensions(spl_autoload_extensions() . ",.phpt");
MyTester\Bridges\NetteDI\TestsRunner::$autoloader = ?;
spl_autoload_register(?);', [$this->suits, __NAMESPACE__ . "\\autoload"]);
    foreach($container->findByTag(self::TAG) as $suit => $foo) {
      $initialize->addBody('$runner->addSuit($this->getService(?));', [$suit]);
    }
  }
}
?>
