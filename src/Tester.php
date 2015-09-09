<?php
namespace MyTester;

/**
 * Automated tests runner
 * 
 * @author Jakub Konečný
 * @copyright (c) 2015, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
class Tester {
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
   * @return array
   */
  protected function findSuits($folder) {
    $suits = array();
    $robot = new \Nette\Loaders\RobotLoader;
    $robot->cacheStorage = new \Nette\Caching\Storages\DevNullStorage;
    $robot->addDirectory($folder);
    $robot->acceptFiles = "*.phpt";
    $robot->rebuild();
    $robot->register();
    $classes = $robot->indexedClasses;
    foreach(array_keys($classes) as $class) {
      $rc = new \ReflectionClass($class);
      if(!$rc->isAbstract() AND $rc->isSubclassOf("MyTester\TestCase")) {
        $suits[] = $rc->getName();
      }
    }
    return $suits;
  }
  
  /**
   * @return void
   */
  function execute() {
    foreach($this->suits as $class) {
      $suit = new $class;
      $suit->run();
    }
  }
}
?>