<?php
declare(strict_types=1);

namespace MyTester;

use ReflectionClass;
use ReflectionException;

/**
 * @author Jakub Konečný
 * @internal
 */
abstract class BaseTestsSuitesFinder implements ITestsSuitesFinder
{
    protected function isTestSuite(string $class): bool
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            return false;
        }
        return !$reflection->isAbstract() && $reflection->isSubclassOf(TestCase::class);
    }
}
