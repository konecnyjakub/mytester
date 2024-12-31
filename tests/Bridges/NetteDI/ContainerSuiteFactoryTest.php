<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\Attributes\TestSuite;
use MyTester\InvalidTestSuiteException;
use MyTester\TestCase;

/**
 * Test suite for class ContainerSuiteFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("ContainerSuiteFactoryTest")]
final class ContainerSuiteFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        // @phpstan-ignore argument.type
        $factory = new ContainerSuiteFactory([
            new static(),
            new \stdClass(),
        ]);
        $this->assertType(self::class, $factory->create(self::class));
        $this->assertThrowsException(function () use ($factory) {
            $factory->create(\stdClass::class);
        }, InvalidTestSuiteException::class, "stdClass is not a descendant of MyTester\\TestCase.");
        $this->assertThrowsException(function () use ($factory) {
            // @phpstan-ignore argument.type
            $factory->create("abcd");
        }, InvalidTestSuiteException::class, "abcd is not a descendant of MyTester\\TestCase.");
    }
}
