<?php
declare(strict_types=1);

namespace MyTester;

use Nette\Utils\Finder;
use Nette\Utils\FileSystem;

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
  
  public function __construct(string $folder) {
    $this->suits = $this->findSuits($folder);
  }
  
  /**
   * Find test suits to run
   */
  private function findSuits(string $folder): array {
    $suits = [];
    $robot = new \Nette\Loaders\RobotLoader();
    $tempDir = "$folder/temp/cache/Robot.Loader";
    if(is_dir("$folder/_temp")) {
      $tempDir = "$folder/_temp/cache/Robot.Loader";
    }
    FileSystem::createDir($tempDir);
    $robot->setTempDirectory($tempDir);
    $robot->addDirectory($folder);
    $robot->acceptFiles = ["*.phpt"];
    $robot->rebuild();
    $robot->register();
    $classes = $robot->getIndexedClasses();
    foreach($classes as $class => $file) {
      if(!class_exists($class)) {
        continue;
      }
      $rc = new \ReflectionClass($class);
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
      /** @var TestCase $suit */
      $suit = new $suit[0]();
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
      Environment::printLine("OK");
    }
    exit((int) $failed);
  }
}
?>