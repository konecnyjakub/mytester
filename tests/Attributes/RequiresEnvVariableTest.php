<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use MyTester\TestCase;

/**
 * Test suite for class RequiresEnvVariable
 *
 * @author Jakub Konečný
 */
#[TestSuite("RequiresEnvVariable")]
#[Group("attributes")]
final class RequiresEnvVariableTest extends TestCase
{
    public function testGetSkipValue(): void
    {
        $varName = "TEST_ENV";
        $value = "ABC";
        $attribute = new RequiresEnvVariable($varName);
        $this->assertSame("env variable $varName is not set", $attribute->getSkipValue());

        $_ENV[$varName] = $value;
        $attribute = new RequiresEnvVariable($varName);
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresEnvVariable($varName, "DEF");
        $this->assertSame("value of env variable $varName is not DEF", $attribute->getSkipValue());

        $attribute = new RequiresEnvVariable($varName, $value);
        $this->assertNull($attribute->getSkipValue());

        unset($_ENV[$varName]);
    }
}
