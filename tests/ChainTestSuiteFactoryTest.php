<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class ChainTestSuiteFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("ChainTestSuiteFactoryTest")]
#[Group("testSuitesFactories")]
final class ChainTestSuiteFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new ChainTestSuiteFactory();
        $this->assertNull($factory->create(self::class));
        $this->assertNull($factory->create(\stdClass::class));

        $factory->registerFactory(new class implements ITestSuiteFactory
        {
            public function create(string $className): ?TestCase
            {
                return null;
            }
        });
        $this->assertNull($factory->create(self::class));
        $this->assertNull($factory->create(\stdClass::class));

        $factory->registerFactory(new SimpleTestSuiteFactory());
        $this->assertType(self::class, $factory->create(self::class));
        $this->assertThrowsException(function () use ($factory) {
            $factory->create(\stdClass::class);
        }, InvalidTestSuiteException::class, "stdClass is not a descendant of " . TestCase::class . ".");
    }
}
