<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use MyTester\TestCase;

/**
 * Test suite for class RequiresPhpVersion
 *
 * @author Jakub Konečný
 */
#[TestSuite("RequiresPhpVersion")]
#[Group("attributes")]
final class RequiresPhpVersionTest extends TestCase
{
    public function testGetSkipValue(): void
    {
        $attribute = new RequiresPhpVersion("8.3");
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresPhpVersion("666");
        $this->assertSame("PHP >=666 is required", $attribute->getSkipValue());

        $attribute = new RequiresPhpVersion("8.3.0", "<");
        $this->assertSame("PHP <8.3.0 is required", $attribute->getSkipValue());
    }
}
