<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\ISkipAttribute;

/**
 * Skip attribute
 * Allows skipping a test unconditionally
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class Skip implements ISkipAttribute
{
    public function getValue(): string
    {
        return "";
    }

    public function getSkipValue(): string
    {
        return $this->getValue();
    }
}
