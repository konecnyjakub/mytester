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
     * @param mixed[] $data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * @return mixed[]
     */
    public function getValue(): array
    {
        return $this->data;
    }
}
