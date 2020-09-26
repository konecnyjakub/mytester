<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

require_once __DIR__ . "/../../functions.php";

use MyTester\TestCase;
use MyTester\Environment;

/**
 * Tests Runner
 *
 * @author Jakub Konečný
 * @method void onExecute()
 */
final class TestsRunner {
  use \Nette\SmartObject;

  /** @var TestCase[] */
  private array $suits = [];
  public static array $autoloader = [];
  /** @var callable[] */
  public array $onExecute = [];
  
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
    Environment::printResults();
    return $failed;
  }
}
?>