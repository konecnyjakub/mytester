<?php
declare(strict_types=1);

namespace MyTester;

use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;
use ReflectionClass;

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
    $this->suits = $this->findSuits($folder);
    $this->testSuitFactory = new class implements ITestSuitFactory {
      public function create(string $className): TestCase {
        return new $className();
      }
    };
  }
  
  /**
   * Find test suits to run
   */
  private function findSuits(string $folder): array {
    $suits = [];
    $robot = new RobotLoader();
    $tempDir = "$folder/temp/cache/Robot.Loader";
    if(is_dir("$folder/_temp")) {
      $tempDir = "$folder/_temp/cache/Robot.Loader";
    }
    FileSystem::createDir($tempDir);
    $robot->setTempDirectory($tempDir);
    $robot->addDirectory($folder);
    $robot->acceptFiles = ["*Test.php", "*.phpt", ];
    $robot->rebuild();
    $robot->register();
    $classes = $robot->getIndexedClasses();
    foreach($classes as $class => $file) {
      if(!class_exists($class)) {
        continue;
      }
      $rc = new ReflectionClass($class);
      if(!$rc->isAbstract() && $rc->isSubclassOf(TestCase::class)) {
        $suits[] = [$rc->getName(), $file];
      }
    }
    return $suits;
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