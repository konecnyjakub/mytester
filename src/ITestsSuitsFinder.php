<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ITestsSuitsFinder {
  public function getSuits(string $folder): array;
}
?>