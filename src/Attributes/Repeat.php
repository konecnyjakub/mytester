<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use ValueError;

/**
 * Retry attribute
 * The test should be repeated x times until it fails
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Repeat
{
    public function __construct(private int $maxRetries)
    {
        if ($this->maxRetries < 2) {
            throw new ValueError("A test has to be repeated at least 2 times.");
        }
    }

    public function getValue(): int
    {
        return $this->maxRetries;
    }
}
