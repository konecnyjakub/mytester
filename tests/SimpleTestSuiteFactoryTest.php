<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\PHPT\PHPTTestCase;

/**
 * Test suite for class SimpleTestSuiteFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("SimpleTestSuiteFactoryTest")]
#[Group("testSuitesFactories")]
final class SimpleTestSuiteFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new SimpleTestSuiteFactory();
        $this->assertType(self::class, $factory->create(self::class));
        $this->assertNull($factory->create(PHPTTestCase::class));
        $this->assertThrowsException(function () use ($factory) {
            $factory->create(\stdClass::class);
        }, InvalidTestSuiteException::class, "stdClass is not a descendant of " . TestCase::class . ".");
    }
}
