<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;

/**
 * Flaky test attribute
 * Marks a test as flaky
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class FlakyTest
{
    public function __construct(private int $maxRetries = 2)
    {
    }

    public function getValue(): int
    {
        return $this->maxRetries;
    }
}
