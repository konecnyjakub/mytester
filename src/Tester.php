<?php
declare(strict_types=1);

namespace MyTester;

use Jean85\PrettyVersions;
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

  private const PACKAGE_NAME = "konecnyjakub/mytester";
  
  /** @var string[] */
  private array $suits;
  /** @var callable[] */
  public array $onExecute = [
    Environment::class . "::setup",
  ];
  public ITestSuitFactory $testSuitFactory;
  private string $folder;
  
  public function __construct(string $folder) {
    $this->onExecute[] = [$this, "printInfo"];
    $this->suits = (new TestSuitsFinder())->getSuits($folder);
    $this->testSuitFactory = new class implements ITestSuitFactory {
      public function create(string $className): TestCase {
        return new $className();
      }
    };
    $this->folder = $folder;
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

  /**
   * Print version of My Tester and PHP
   */
  private function printInfo(): void {
    echo "My Tester " . PrettyVersions::getVersion(static::PACKAGE_NAME) . "\n";
    echo "\n";
    echo "PHP " . PHP_VERSION . "(" . PHP_SAPI . ")\n";
    echo "\n";
  }
}
?>