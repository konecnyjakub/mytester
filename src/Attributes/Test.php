<?php

declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Test attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Test extends BaseAttribute
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
