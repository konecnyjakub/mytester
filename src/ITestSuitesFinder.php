<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestSuitesFinder
{
    /**
     * @return class-string[]
     */
    public function getSuites(TestSuitesSelectionCriteria $criteria): array;
}
