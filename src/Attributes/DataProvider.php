<?php

declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Data provider attribute
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class DataProvider extends BaseAttribute
{
    public string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
