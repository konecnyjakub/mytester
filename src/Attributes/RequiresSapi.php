<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\ISkipAttribute;

/**
 * Requires sapi attribute
 * Defines PHP sapi required for a test (suite)
 *
 * @author Jakub Konečný
 * @see PHP_SAPI
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class RequiresSapi implements ISkipAttribute
{
    public function __construct(private string $sapiName)
    {
    }

    public function getValue(): string
    {
        return $this->sapiName;
    }

    public function getSkipValue(): ?string
    {
        if (PHP_SAPI !== $this->sapiName) {
            return "the sapi is not $this->sapiName";
        }
        return null;
    }
}
