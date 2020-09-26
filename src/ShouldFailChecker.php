<?php
declare(strict_types=1);

namespace MyTester;

use Nette\Reflection\Method;

/**
 * ShouldFailChecker
 *
 * @author Jakub Konečný
 * @internal
 */
final class ShouldFailChecker {
  use \Nette\SmartObject;

  public const ANNOTATION_NAME = "fail";

  public function shouldFail(string $class, string $method): bool {
    $reflection = new Method($class, $method);
    return $reflection->hasAnnotation(static::ANNOTATION_NAME);
  }
}
?>