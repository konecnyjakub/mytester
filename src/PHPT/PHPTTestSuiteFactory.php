<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\InvalidTestSuiteException;
use MyTester\TestSuiteFactory;
use MyTester\TestCase;
use MyTester\TestsFolderProvider;

final readonly class PHPTTestSuiteFactory implements TestSuiteFactory
{
    public function __construct(private PhptRunner $runner, private TestsFolderProvider $testsFolderProvider)
    {
    }

    public function create(string $className): ?TestCase
    {
        if (!is_subclass_of($className, TestCase::class)) {
            throw new InvalidTestSuiteException("$className is not a descendant of " . TestCase::class . ".");
        }
        if ($className !== PHPTTestCase::class) {
            return null;
        }
        return new PHPTTestCase($this->runner, $this->testsFolderProvider);
    }
}
