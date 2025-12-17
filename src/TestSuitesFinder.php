<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface TestSuitesFinder
{
    /**
     * @return class-string[]
     */
    public function getSuites(TestSuitesSelectionCriteria $criteria): array;
}
