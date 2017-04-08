<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

require_once __DIR__ . "/../../functions.php";

use MyTester\TestCase,
    MyTester\Environment,
    Nette\Utils\Finder;

/**
 * Tests Runner
 *
 * @author Jakub Konečný
 * @copyright (c) 2016-2017, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 * @method void onExecute()
 */
class TestsRunner {
  use \Nette\SmartObject;
  
  /** @var TestCase[] */
  protected $suits = [];
  /** @var array */
  static public $autoloader = [];
  /** @var array */
  public $onExecute = [];
  
  /**
   * @param TestCase $suit
   * @return void
   */
  function addSuit(TestCase $suit): void {
    $this->suits[] = $suit;
  }
  
  /**
   * Autoloader for test suits
   *
   * @param string $class
   * @return void
   */
  static function autoload(string $class): void {
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
  function execute(): bool {
    $this->onExecute();
    $failed = false;
    foreach($this->suits as $suit) {
      $result = $suit->run();
      if(!$result) {
        $failed = true;
      }
    }
    Environment::printLine("");
    foreach(Environment::getSkipped() as $skipped) {
      if($skipped["reason"]) {
        $reason = ": {$skipped["reason"]}";
      } else {
        $reason = "";
      }
      Environment::printLine("Skipped {$skipped["name"]}$reason");
    }
    if($failed) {
      Environment::printLine("Failed");
      Environment::printLine("");
      $files = Finder::findFiles("*.errors")->in(\getTestsDirectory());
      foreach($files as $name => $file) {
        Environment::printLine("--- ". substr($file->getBaseName(), 0, -7));
        echo file_get_contents($name);
      }
    } else {
      echo "OK";
    }
    return $failed;
  }
}
?>