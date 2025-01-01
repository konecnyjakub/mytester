<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use MyTester\TestCase;

/**
 * Test suite for class RequiresPackage
 *
 * @author Jakub Konečný
 */
#[TestSuite("RequiresPackage")]
final class RequiresPackageTest extends TestCase
{
    public function testGetSkipValue(): void
    {
        $attribute = new RequiresPackage("composer/semver");
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresPackage("composer/semver", "^3.0");
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresPackage("phpunit/phpunit");
        $this->assertSame("package phpunit/phpunit is not installed", $attribute->getSkipValue());

        $attribute = new RequiresPackage("phpstan/phpstan", "^1.0");
        $this->assertSame("package phpstan/phpstan is not installed in version ^1.0", $attribute->getSkipValue());
    }
}
