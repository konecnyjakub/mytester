<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestSuitesFinder
{
    public const FILENAME_SUFFIX = "Test.php";

    /**
     * @return string[]
     */
    public function getSuites(string $folder): array;
}
