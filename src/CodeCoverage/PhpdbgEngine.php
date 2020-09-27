<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Phpdbg engine for code coverage collector
 *
 * @author Jakub Konečný
 * @internal
 */
final class PhpdbgEngine implements \MyTester\ICodeCoverageEngine {
  public function getName(): string {
    return "phpdbg";
  }

  public function isAvailable(): bool {
    return PHP_SAPI === "phpdbg";
  }

  public function start(): void {
    phpdbg_start_oplog();
  }

  public function collect(): array {
    $positive = phpdbg_end_oplog();
    $negative = phpdbg_get_executable();

    foreach($positive as $file => &$lines) {
      $lines = array_fill_keys(array_keys($lines), 1);
    }

    foreach($negative as $file => &$lines) {
      $lines = array_fill_keys(array_keys($lines), -1);
    }

    return [$positive, $negative ];
  }
}
?>