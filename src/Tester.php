<?php
namespace MyTester;

/**
 * Automated tests runner
 * 
 * @author Jakub Konečný
 * @copyright (c) 2015-2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 * @property-read array $suits
 */
class Tester {
  use \Nette\SmartObject;
  
  /** @var array */
  protected $suits;
  
  /**
   * @param string $folder
   * @param string $output screen/file
   */
  function __construct($folder) {
    $this->suits = $this->findSuits($folder);
  }
  
  /**
   * Find test suits to run
   * 
   * @param string $folder Where to look
   * @return string[]
   */
  protected function findSuits($folder) {
    $suits = [];
    $robot = new \Nette\Loaders\RobotLoader;
    $robot->setCacheStorage(new \Nette\Caching\Storages\DevNullStorage);
    $robot->addDirectory($folder);
    $robot->acceptFiles = "*.phpt";
    $robot->rebuild();
    $robot->register();
    $classes = $robot->getIndexedClasses();
    foreach($classes as $class => $file) {
      $rc = new \Nette\Reflection\ClassType($class);
      if(!$rc->isAbstract() AND $rc->isSubclassOf(TestCase::class)) {
        $suits[] = [$rc->getName(), $file];
      }
    }
    return $suits;
  }
  
  /**
   * @return string[]
   */
  function getSuits() {
    return $this->suits;
  }
  
  /**
   * Execute all tests
   * 
   * @return void
   */
  function execute() {
    foreach($this->suits as $suit) {
      $suit = new $suit[0];
      $suit->run();
    }
  }
}
?>