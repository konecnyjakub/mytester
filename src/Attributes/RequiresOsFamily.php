<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\ISkipAttribute;

/**
 * Requires os family attribute
 * Defines os family required for a test
 *
 * @author Jakub Konečný
 * @see PHP_OS_FAMILY
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class RequiresOsFamily implements ISkipAttribute
{
    public function __construct(private string $osFamilyName)
    {
    }

    public function getValue(): string
    {
        return $this->osFamilyName;
    }

    public function getSkipValue(): ?string
    {
        if (PHP_OS_FAMILY !== $this->osFamilyName) {
            return "os family is not $this->osFamilyName";
        }
        return null;
    }
}
