<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Data attribute
 * Defines one data set for a test, can be used multiple times
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Data
{
    /**
     * @param mixed[] $value
     */
    public function __construct(public array $value)
    {
    }
}
