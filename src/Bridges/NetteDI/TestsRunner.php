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
 * @internal
 */
final class TestsRunner {
  use \Nette\SmartObject;

  /** @var TestCase[] */
  private array $suits = [];
  /** @var callable[] */
  public array $onExecute = [];
  
  public function addSuit(TestCase $suit): void {
    $this->suits[] = $suit;
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