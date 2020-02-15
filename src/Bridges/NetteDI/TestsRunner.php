<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

require_once __DIR__ . "/../../functions.php";

use MyTester\TestCase;
use MyTester\Environment;
use Nette\Utils\Finder;

/**
 * Tests Runner
 *
 * @author Jakub Konečný
 * @method void onExecute()
 */
final class TestsRunner {
  use \Nette\SmartObject;
  
  /** @var TestCase[] */
  protected $suits = [];
  /** @var array */
  public static $autoloader = [];
  /** @var array */
  public $onExecute = [];
  
  public function addSuit(TestCase $suit): void {
    $this->suits[] = $suit;
  }
  
  /**
   * Autoloader for test suits
   */
  public static function autoload(string $class): void {
    foreach(static::$autoloader as $suit) {
      if($suit[0] === $class) {
        require $suit[1];
        return;
      }
    }
  }
  
  public function execute(): bool {
    $this->onExecute();
    $failed = false;
    foreach($this->suits as $suit) {
      if(!$suit->run()) {
        $failed = true;
      }
    }
    Environment::printLine("");
    foreach(Environment::getSkipped() as $skipped) {
      $reason = "";
      if($skipped["reason"]) {
        $reason = ": {$skipped["reason"]}";
      }
      Environment::printLine("Skipped {$skipped["name"]}$reason");
    }
    if($failed) {
      Environment::printLine("Failed");
      Environment::printLine("");
      $files = Finder::findFiles("*.errors")->in(\getTestsDirectory());
      /** @var \SplFileInfo $file */
      foreach($files as $name => $file) {
        Environment::printLine("--- " . substr($file->getBasename(), 0, -7));
        echo file_get_contents($name);
      }
    } else {
      echo "OK";
    }
    return $failed;
  }
}
?>