<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use ReflectionMethod;

/**
 * DataProvider
 *
 * @author Jakub Konečný
 * @internal
 */
final class DataProvider {
  use \Nette\SmartObject;

  public const ANNOTATION_NAME = "data";

  private Reader $annotationsReader;

  public function __construct(Reader $annotationsReader) {
    $this->annotationsReader = $annotationsReader;
  }

  public function getData(string $class, string $method): array {
    $reflection = new ReflectionMethod($class, $method);
    if($reflection->getNumberOfParameters() < 1) {
      return [];
    }
    /** @var mixed $value */
    $value = $this->annotationsReader->getAnnotation(static::ANNOTATION_NAME, $class, $method);
    return (array) $value;
  }
}
?>