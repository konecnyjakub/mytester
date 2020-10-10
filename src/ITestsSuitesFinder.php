<?php

declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestsSuitesFinder
{
    /**
     * @return string[]
     */
    public function getSuites(string $folder): array;
}
