<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;

/**
 * ShouldFailChecker
 *
 * @author Jakub Konečný
 * @internal
 */
final class ShouldFailChecker {
  use \Nette\SmartObject;

  public const ANNOTATION_NAME = "fail";

  private Reader $annotationsReader;

  public function __construct(Reader $annotationsReader) {
    $this->annotationsReader = $annotationsReader;
  }

  public function shouldFail(string $class, string $method): bool {
    return $this->annotationsReader->hasAnnotation(static::ANNOTATION_NAME, $class, $method);
  }
}
?>