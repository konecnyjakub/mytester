<?php

declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Fail attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Fail extends BaseAttribute
{
    public mixed $value;

    public function __construct(mixed $value = null)
    {
        $this->value = $value;
    }
}
