<?php
declare(strict_types=1);

namespace MyTester\Annotations\Attributes;

/**
 * Test attribute
 *
 * @author Jakub Konečný
 * @internal
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Test {
  public string $value;

  public function __construct(string $value) {
    $this->value = $value;
  }
}
?>