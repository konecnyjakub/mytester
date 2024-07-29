<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class TestSuiteFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("TestSuiteFactoryTest")]
final class TestSuiteFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new TestSuiteFactory();
        $this->assertType(static::class, $factory->create(static::class));
        $this->assertThrowsException(function () use ($factory) {
            $factory->create(\stdClass::class);
        }, InvalidTestCaseException::class, "stdClass is not a descendant of MyTester\\TestCase.");
    }
}
