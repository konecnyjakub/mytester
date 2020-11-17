<?php

declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 * @internal
 */
final class TestSuiteFactory implements ITestSuiteFactory
{
    public function create(string $className): TestCase
    {
        if (!is_subclass_of($className, TestCase::class)) {
            throw new InvalidTestCaseException("$className is not a descendant of " . TestCase::class . ".");
        }
        return new $className();
    }
}
