<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\SkipAttribute;

/**
 * Requires PHP version attribute
 * Defines minimal PHP version required for a test (suite)
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class RequiresPhpVersion implements SkipAttribute
{
    public function __construct(private string $version, private string $operator = ">=")
    {
    }

    public function getValue(): string
    {
        return $this->version;
    }

    public function getSkipValue(): ?string
    {
        if (!version_compare(PHP_VERSION, $this->version, $this->operator)) {
            return "PHP $this->operator$this->version is required";
        }
        return null;
    }
}
