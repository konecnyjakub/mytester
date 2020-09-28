<?php
declare(strict_types=1);

namespace MyTester;

use hanneskod\classtools\Iterator\ClassIterator;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ComposerTestSuitsFinder implements ITestsSuitsFinder {
  public function getSuits(string $folder): array {
    $suits = [];
    $finder = new Finder();
    $iterator = new ClassIterator($finder->in($folder));
    $iterator->enableAutoloading();
    /** @var ReflectionClass $class */
    foreach($iterator->type(TestCase::class) as $class) {
      $filename = (string) $class->getFileName();
      if(!$class->isInstantiable() || !str_ends_with($filename, "Test.php")) {
        continue;
      }
      $suits[] = [$class->getName(), $filename];
    }
    return $suits;
  }
}
?>