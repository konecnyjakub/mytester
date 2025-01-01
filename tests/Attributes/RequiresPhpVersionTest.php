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
final class RequiresPhpVersionTest extends TestCase
{
    public function testGetSkipValue(): void
    {
        $attribute = new RequiresPhpVersion("8.3");
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresPhpVersion("666");
        $this->assertSame("PHP version is lesser than 666", $attribute->getSkipValue());
    }
}
