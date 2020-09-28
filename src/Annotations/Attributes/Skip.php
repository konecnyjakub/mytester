<?php
declare(strict_types=1);

namespace MyTester\Annotations\Attributes;

/**
 * Skip attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Skip {
  use \Nette\SmartObject;

  /** @var mixed */
  public $value;

  /**
   * @param mixed $value
   */
  public function __construct($value = true) {
    $this->value = $value;
  }
}
?>