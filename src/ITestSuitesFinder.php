<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestSuitesFinder
{
    public const string FILENAME_SUFFIX = "Test.php";

    /**
     * @return class-string[]
     */
    public function getSuites(string $folder): array;
}
