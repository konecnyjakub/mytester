<?php
declare(strict_types=1);

namespace MyTester\Annotations\Attributes;

/**
 * Data attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Data {
  public array $value;

  public function __construct(array $value) {
    $this->value = $value;
  }
}
?>