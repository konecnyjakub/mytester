<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestSuiteFactory {
  public function create(string $className): TestCase;
}
?>