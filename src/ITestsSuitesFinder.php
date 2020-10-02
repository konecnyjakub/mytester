<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ITestsSuitesFinder {
  public function getSuites(string $folder): array;
}
?>