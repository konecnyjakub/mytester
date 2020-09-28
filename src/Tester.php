<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Bridges\NetteRobotLoader\TestSuitsFinder;

/**
 * Automated tests runner
 * 
 * @author Jakub Konečný
 * @property-read string[] $suits
 * @method void onExecute()
 */
final class Tester {
  use \Nette\SmartObject;
  
  /** @var string[] */
  private array $suits;
  /** @var callable[] */
  public array $onExecute = [
    Environment::class . "::setup",
    Environment::class . "::printInfo",
  ];
  public ITestSuitFactory $testSuitFactory;
  
  public function __construct(string $folder) {
    $this->suits = (new TestSuitsFinder())->getSuits($folder);
    $this->testSuitFactory = new class implements ITestSuitFactory {
      public function create(string $className): TestCase {
        return new $className();
      }
    };
  }
  
  /**
   * @return string[]
   */
  protected function getSuits(): array {
    return $this->suits;
  }
  
  /**
   * Execute all tests
   */
  public function execute(): void {
    $this->onExecute();
    $failed = false;
    foreach($this->suits as $suit) {
      $suit = $this->testSuitFactory->create($suit[0]);
      if(!$suit->run()) {
        $failed = true;
      }
    }
    Environment::printResults();
    exit((int) $failed);
  }
}
?>