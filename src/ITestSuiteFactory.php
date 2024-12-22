<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestSuiteFactory
{
    /**
     * @param class-string $className
     */
    public function create(string $className): TestCase;
}
