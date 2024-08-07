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
    protected function isTestSuite(string $class): bool
    {
        try {
            $reflection = new ReflectionClass($class); // @phpstan-ignore argument.type
        } catch (ReflectionException) {
            return false;
        }
        return !$reflection->isAbstract() && $reflection->isSubclassOf(TestCase::class);
    }
}
