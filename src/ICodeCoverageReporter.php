<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 * @internal
 */
interface ICodeCoverageReporter {
  public function render(array $data): string;
}
?>