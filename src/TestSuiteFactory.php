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
        return new $className();
    }
}
