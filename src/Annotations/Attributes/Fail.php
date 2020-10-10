<?php

declare(strict_types=1);

namespace MyTester\Annotations\Attributes;

use Attribute;

/**
 * Fail attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Fail extends BaseAttribute
{
    /** @var mixed */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }
}
