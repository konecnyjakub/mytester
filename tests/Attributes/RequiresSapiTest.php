<?php
declare(strict_types=1);

namespace MyTester\Attributes;

use MyTester\TestCase;

/**
 * Test suite for class RequiresSapi
 *
 * @author Jakub Konečný
 */
#[TestSuite("RequiresSapi")]
#[Group("attributes")]
final class RequiresSapiTest extends TestCase
{
    public function testGetSkipValue(): void
    {
        $attribute = new RequiresSapi(PHP_SAPI);
        $this->assertNull($attribute->getSkipValue());

        $attribute = new RequiresSapi("abc");
        $this->assertSame("the sapi is not abc", $attribute->getSkipValue());
    }
}
