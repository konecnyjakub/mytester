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
 */
class TestsRunner {
  use \Nette\SmartObject;
  
  /** @var TestCase[] */
  protected $suits = [];
  /** @var array */
  static $autoloader = [];
  
  /**
   * @param TestCase $suit
   * @return void
   */
  function addSuit(TestCase $suit) {
    $this->suits[] = $suit;
  }
  
  /**
   * @return void
   */
  function execute() {
    Environment::setup();
    Environment::printInfo();
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
  }
}
?>