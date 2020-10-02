<?php
declare(strict_types=1);

namespace MyTester\Annotations\Attributes;

use Attribute;

/**
 * Data attribute
 *
 * @author Jakub Konečný
 * @deprecated
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Data extends BaseAttribute {
  public array $value;

  public function __construct(array $value) {
    $this->value = $value;
  }
}
?>