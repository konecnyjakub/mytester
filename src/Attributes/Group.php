<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Group attribute
 * Assigns a test suite to a group, can be used multiple times
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class Group
{
    public function __construct(private string $name)
    {
    }

    public function getValue(): string
    {
        return $this->name;
    }
}
