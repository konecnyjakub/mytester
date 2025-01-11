<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * TestSuite attribute
 * Provides custom name for a test suite
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class TestSuite
{
    public function __construct(private string $name)
    {
    }

    public function getValue(): string
    {
        return $this->name;
    }
}
