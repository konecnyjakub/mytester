<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use Konecnyjakub\PHPTRunner\Parser;
use Konecnyjakub\PHPTRunner\PhpRunner;
use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use MyTester\InvalidTestSuiteException;
use MyTester\TestCase;
use MyTester\TestsFolderProvider;
use MyTester\TestSuitesSelectionCriteria;

/**
 * Test suite for class PHPTTestSuiteFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("PHPTTestSuiteFactory")]
#[Group("testSuitesFactories")]
#[Group("phpt")]
final class PHPTTestSuiteFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $testsFolderProvider = new TestsFolderProvider(__DIR__);
        $factory = new PHPTTestSuiteFactory(
            new PhptRunner(new Parser(), new PhpRunner()),
            $testsFolderProvider,
            new TestSuitesSelectionCriteria($testsFolderProvider)
        );
        $this->assertType(PHPTTestCase::class, $factory->create(PHPTTestCase::class));
        $this->assertNull($factory->create(self::class));
        $this->assertThrowsException(function () use ($factory) {
            $factory->create(\stdClass::class);
        }, InvalidTestSuiteException::class, "stdClass is not a descendant of " . TestCase::class . ".");
    }
}
