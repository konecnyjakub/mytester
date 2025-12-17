<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\PHPT\PHPTTestCase;

/**
 * Default test suite factory for {@see Tester}
 *
 * Just creates a new instance of given class without passing any parameters
 *
 * @author Jakub Konečný
 */
final class SimpleTestSuiteFactory implements TestSuiteFactory
{
    public function create(string $className): ?TestCase
    {
        if (!is_subclass_of($className, TestCase::class)) {
            throw new InvalidTestSuiteException("$className is not a descendant of " . TestCase::class . ".");
        }
        if ($className === PHPTTestCase::class) {
            return null;
        }
        return new $className();
    }
}
