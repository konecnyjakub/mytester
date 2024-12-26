<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\ISkipAttribute;

/**
 * Requires sapi attribute
 * Defines PHP sapi required required for a test
 *
 * @author Jakub Konečný
 * @see PHP_SAPI
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class RequiresSapi implements ISkipAttribute
{
    public function __construct(public string $value)
    {
    }

    public function getSkipValue(): ?string
    {
        if (PHP_SAPI !== $this->value) {
            return "the sapi is not $this->value";
        }
        return null;
    }
}
