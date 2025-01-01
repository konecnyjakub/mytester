<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use MyTester\TestCase;

/**
 * Test suite for class RequiresPhpExtension
 *
 * @author Jakub Konečný
 */
#[TestSuite("RequiresPhpExtension")]
final class RequiresPhpExtensionTest extends TestCase
{
    public function testGetSkipValue(): void
    {
        $attribute = new RequiresPhpExtension("xml");
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresPhpExtension("abc");
        $this->assertSame("extension abc is not loaded", $attribute->getSkipValue());
    }
}
