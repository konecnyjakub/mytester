<?php
declare(strict_types=1);

namespace MyTester;

use Nette\Reflection\Method;

/**
 * DataProvider
 *
 * @author Jakub Konečný
 * @internal
 */
final class DataProvider {
  use \Nette\SmartObject;

  public const ANNOTATION_NAME = "data";

  public function getData(string $class, string $method): array {
    $reflection = new Method($class, $method);
    /** @var mixed $value */
    $value = $reflection->getAnnotation(static::ANNOTATION_NAME);
    if($reflection->getNumberOfParameters() < 1) {
      return [];
    }
    return (array) $value;
  }
}
?>