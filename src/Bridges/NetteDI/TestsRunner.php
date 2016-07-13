<?php
namespace MyTester\Bridges\NetteDI;

use \MyTester\TestCase;

/**
 * Tests Runner
 *
 * @author Jakub Konečný
 */
class TestsRunner {
  /** @var TestCase[] */
  protected $suits = [];
  
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