<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Data provider attribute
 * Defines a data source for parameters of a test, it is name of a method in the same class
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class DataProvider
{
    public function __construct(private string $methodName)
    {
    }

    public function getValue(): string
    {
        return $this->methodName;
    }
}
