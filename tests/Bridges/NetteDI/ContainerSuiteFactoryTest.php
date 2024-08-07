<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\Attributes\TestSuite;
use MyTester\InvalidTestCaseException;
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
        $this->assertType(static::class, $factory->create(static::class));
        $this->assertThrowsException(function () use ($factory) {
            $factory->create(\stdClass::class);
        }, InvalidTestCaseException::class, "stdClass is not a descendant of MyTester\\TestCase.");
        $this->assertThrowsException(function () use ($factory) {
            // @phpstan-ignore argument.type
            $factory->create("abcd");
        }, InvalidTestCaseException::class, "abcd is not a descendant of MyTester\\TestCase.");
    }
}
