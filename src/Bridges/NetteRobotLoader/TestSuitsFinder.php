<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteRobotLoader;

use MyTester\ITestsSuitsFinder;
use MyTester\TestCase;
use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;
use ReflectionClass;

/**
 * @author Jakub Konečný
 * @internal
 */
final class TestSuitsFinder implements ITestsSuitsFinder {
  public function getSuits(string $folder): array {
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
}
?>