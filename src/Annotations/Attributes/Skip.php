<?php
declare(strict_types=1);

namespace MyTester\Annotations\Attributes;

/**
 * Skip attribute
 *
 * @author Jakub Konečný
 * @internal
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Skip {
  /** @var mixed */
  public $value;

  /**
   * @param mixed $value
   */
  public function __construct($value = null) {
    $this->value = $value;
  }
}
?>