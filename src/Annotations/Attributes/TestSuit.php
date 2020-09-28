<?php
declare(strict_types=1);

namespace MyTester\Annotations\Attributes;

/**
 * TestSuit attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class TestSuit {
  public string $value;

  public function __construct(string $value) {
    $this->value = $value;
  }
}
?>