<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class SimpleTestSuiteFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("SimpleTestSuiteFactoryTest")]
final class SimpleTestSuiteFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new SimpleTestSuiteFactory();
        $this->assertType(self::class, $factory->create(self::class));
        $this->assertThrowsException(function () use ($factory) {
            $factory->create(\stdClass::class);
        }, InvalidTestCaseException::class, "stdClass is not a descendant of " . TestCase::class . ".");
    }
}
