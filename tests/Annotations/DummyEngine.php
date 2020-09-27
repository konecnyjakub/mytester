<?php
declare(strict_types=1);

namespace MyTester\Annotations;

/**
 * Dummy engine for annotations reader
 *
 * @author Jakub Konečný
 */
final class DummyEngine implements \MyTester\IAnnotationsReaderEngine {
  public function hasAnnotation(string $name, $class, string $method = null): bool {
    return true;
  }

  public function getAnnotation(string $name, $class, string $method = null) {
    return "abc";
  }
}
?>