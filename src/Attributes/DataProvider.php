<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Data provider attribute
 * Defines a data source for parameters of a test
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class DataProvider
{
    public function __construct(public string $value)
    {
    }
}
