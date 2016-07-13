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
  /** @var TestCase[] */
  protected $suits = [];
  /** @var array */
  static $autoloader = [];
  
  function addSuit(TestCase $suit) {
    $this->suits[] = $suit;
  }
  
  function execute() {
    foreach($this->suits as $suit) {
      $suit->run();
    }
  }
}
?>