<?php
declare(strict_types=1);

namespace MyTester;

use ReflectionClass;
use ReflectionException;

/**
 * @author Jakub Konečný
 * @internal
 */
abstract class BaseTestSuitesFinder implements ITestSuitesFinder
{
    /**
     * @param class-string $class
     */
    protected function isTestSuite(string $class): bool
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException) { // @phpstan-ignore catch.neverThrown
            return false;
        }
        return !$reflection->isAbstract() && $reflection->isSubclassOf(TestCase::class);
    }
}
