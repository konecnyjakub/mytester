<?php
namespace MyTester\Bridges\NetteDI;

use MyTester\TestCase,
    MyTester\Environment;

/**
 * Tests Runner
 *
 * @author Jakub Konečný
 * @copyright (c) 2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 * @method void onExecute()
 */
class TestsRunner {
  use \Nette\SmartObject;
  
  /** @var TestCase[] */
  protected $suits = [];
  /** @var array */
  static $autoloader = [];
  /** @var array */
  public $onExecute = [];
  
  /**
   * @param TestCase $suit
   * @return void
   */
  function addSuit(TestCase $suit) {
    $this->suits[] = $suit;
  }
  
  /**
   * Autoloader for test suits
   *
   * @param string $class
   * @return void
   */
  static function autoload($class) {
    foreach(static::$autoloader as $suit) {
      if($suit[0] === $class) {
        require $suit[1];
        return;
      }
    }
  }
  
  /**
   * @return bool
   */
  function execute() {
    $this->onExecute();
    $failed = false;
    foreach($this->suits as $suit) {
      $result = $suit->run();
      if(!$result) $failed = true;
    }
    Environment::printLine("");
    foreach(Environment::getSkipped() as $skipped) {
      if($skipped["reason"]) $reason = ": {$skipped["reason"]}";
      else $reason = "";
      Environment::printLine("Skipped {$skipped["name"]}$reason");
    }
    if($failed) {
      echo "Failed";
    } else {
      echo "OK";
    }
    return $failed;
  }
}
?>
