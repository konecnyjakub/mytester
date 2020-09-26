<?php
declare(strict_types=1);

namespace MyTester\Annotations;

use Nette\Reflection\ClassType;
use Nette\Reflection\Method;

/**
 * NetteReflectionEngine
 *
 * @author Jakub Konečný
 * @internal
 */
final class NetteReflectionEngine implements \MyTester\IAnnotationsReaderEngine {
  public function hasAnnotation(string $name, $class, string $method = null): bool {
    if($method !== null) {
      $reflection = new Method($class, $method);
    } else {
      $reflection = ClassType::from($class);
    }
    return $reflection->hasAnnotation($name);
  }

  public function getAnnotation(string $name, $class, string $method = null) {
    if($method !== null) {
      $reflection = new Method($class, $method);
    } else {
      $reflection = ClassType::from($class);
    }
    return $reflection->getAnnotation($name);
  }
}
?>