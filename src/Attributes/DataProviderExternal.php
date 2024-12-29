<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Data provider external attribute
 * Defines a data source for parameters of a test, it is a class name and name of a static method in that class
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class DataProviderExternal
{
    public function __construct(public string $className, public string $methodName)
    {
    }

    public function getValue(): string
    {
        return $this->className . "::" . $this->methodName;
    }
}
