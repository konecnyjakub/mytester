<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\ISkipAttribute;

/**
 * Requires PHP version attribute
 * Defines minimal PHP version required for a test
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD)]
final readonly class RequiresPhpVersion implements ISkipAttribute
{
    public function __construct(private string $version)
    {
    }

    public function getValue(): string
    {
        return $this->version;
    }

    public function getSkipValue(): ?string
    {
        if (version_compare(PHP_VERSION, $this->version, "<")) {
            return "PHP version is lesser than $this->version";
        }
        return null;
    }
}
