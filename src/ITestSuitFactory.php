<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 */
interface ITestSuitFactory {
  public function create(string $className): TestCase;
}
?>