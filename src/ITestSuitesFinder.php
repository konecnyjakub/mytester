<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestSuitesFinder
{
    /**
     * @deprecated
     */
    public const string FILENAME_SUFFIX = "Test.php";

    /**
     * @return class-string[]
     */
    public function getSuites(TestSuitesSelectionCriteria $criteria): array;
}
