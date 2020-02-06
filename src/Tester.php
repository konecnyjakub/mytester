<?php
declare(strict_types=1);

namespace MyTester;

use Nette\Utils\Finder;
use Nette\Utils\FileSystem;

/**
 * Automated tests runner
 * 
 * @author Jakub Konečný
 * @copyright (c) 2015-2019, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 * @property-read array $suits
 * @method void onExecute()
 */
final class Tester {
  use \Nette\SmartObject;
  
  /** @var array */
  protected $suits;
  /** @var callable[] */
  public $onExecute = [
    Environment::class . "::setup",
    Environment::class . "::printInfo",
  ];
  
  public function __construct(string $folder) {
    $this->suits = $this->findSuits($folder);
  }
  
  /**
   * Find test suits to run
   */
  protected function findSuits(string $folder): array {
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
      $rc = new \Nette\Reflection\ClassType($class);
      if(!$rc->isAbstract() && $rc->isSubclassOf(TestCase::class)) {
        $suits[] = [$rc->getName(), $file];
      }
    }
    return $suits;
  }
  
  /**
   * @return string[]
   */
  public function getSuits(): array {
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