<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use MyTester\TestCase;

/**
 * Test suite for class RequiresOsFamily
 *
 * @author Jakub Konečný
 */
#[TestSuite("RequiresOsFamily")]
#[Group("attributes")]
final class RequiresOsFamilyTest extends TestCase
{
    public function testGetSkipValue(): void
    {
        $attribute = new RequiresOsFamily(PHP_OS_FAMILY);
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresOsFamily("Solaris");
        $this->assertSame("os family is not Solaris", $attribute->getSkipValue());
    }
}
