<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use Attribute;
use MyTester\SkipAttribute;

/**
 * Requires env variable attribute
 * Defines an env variable required for a test (suite)
 *
 * @author Jakub Konečný
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresEnvVariable implements SkipAttribute
{
    public function __construct(public string $varName, public ?string $varValue = null)
    {
    }

    public function getSkipValue(): ?string
    {
        if (!isset($_ENV[$this->varName])) {
            return "env variable $this->varName is not set";
        } elseif ($this->varValue !== null && $_ENV[$this->varName] !== $this->varValue) {
            return "value of env variable $this->varName is not $this->varValue";
        }
        return null;
    }

    public function getValue(): string
    {
        return $this->varName . (is_string($this->varValue) ? " $this->varValue" : "");
    }
}
