<?php
namespace MyTester\Bridges\NetteDI;

/**
 * MyTester Extension for Nette DIC
 *
 * @author Jakub Konečný
 */
class MyTesterExtension extends \Nette\DI\CompilerExtension {
  const TAG = "mytester.test";
  
  /** @var array */
  private $suits;
  
  /**
   * @return void
   * @throws \Exception
   */
  function loadConfiguration() {
    $config = $this->getConfig();
    $builder = $this->getContainerBuilder();
    if(!isset($config["folder"])) {
      throw new \Exception("No folder is specified.");
    }
    $builder->addDefinition($this->prefix("runner"))
      ->setClass("MyTester\Bridges\NetteDI\TestsRunner");
    $tester = new \MyTester\Tester($config["folder"]);
    $this->suits = $tester->getSuits();
    foreach($this->suits as $index => $suit) {
      $builder->addDefinition($this->prefix($index))
        ->setClass($suit[0])
        ->addTag(self::TAG);
    }
  }
  
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
