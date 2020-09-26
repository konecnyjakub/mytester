<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 * @internal
 */
interface IAnnotationsReaderEngine {
  /**
   * @param string|object $class
   */
  public function hasAnnotation(string $name, $class, string $method = null): bool;

  /**
   * @param string|object $class
   * @return mixed
   */
  public function getAnnotation(string $name, $class, string $method = null);
}
?>