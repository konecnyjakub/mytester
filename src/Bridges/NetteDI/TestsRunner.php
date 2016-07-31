<?php
namespace MyTester\Bridges\NetteDI;

use \MyTester\TestCase;

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
    \MyTester\Environment::printInfo();
    foreach($this->suits as $suit) {
      $suit->run();
    }
  }
}
?>