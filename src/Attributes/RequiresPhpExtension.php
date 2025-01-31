<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\ISkipAttribute;

/**
 * Requires PHP extension attribute
 * Defines a PHP extension required  for a test (suite)
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresPhpExtension implements ISkipAttribute
{
    public function __construct(private string $extensionName)
    {
    }

    public function getValue(): string
    {
        return $this->extensionName;
    }

    public function getSkipValue(): ?string
    {
        if (!extension_loaded($this->extensionName)) {
            return "extension $this->extensionName is not loaded";
        }
        return null;
    }
}
