<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\ISkipAttribute;

/**
 * Requires PHP extension attribute
 * Defines a PHP extension required  for a test
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresPhpExtension implements ISkipAttribute
{
    public function __construct(public string $value)
    {
    }

    public function getSkipValue(): ?string
    {
        if (!extension_loaded($this->value)) {
            return "extension $this->value is not loaded";
        }
        return null;
    }
}
