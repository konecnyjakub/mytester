<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use MyTester\ICodeCoverageEngine;
use RuntimeException;

/**
 * Code coverage collector
 *
 * @author Jakub Konečný
 * @internal
 */
final class Collector {
  /** @var ICodeCoverageEngine[] */
  private array $engines;
  private ?ICodeCoverageEngine $currentEngine = null;

  public function registerEngine(ICodeCoverageEngine $engine): void {
    $this->engines[] = $engine;
  }

  public function start(): void {
    $engine = $this->selectEngine();
    $engine->start();
    register_shutdown_function([$this, "save"]);
  }

  public function save(): void {
    if($this->currentEngine !== null) {
      $this->currentEngine->collect();
    }
  }

  private function selectEngine(): ICodeCoverageEngine {
    if($this->currentEngine !== null) {
      return $this->currentEngine;
    }
    foreach($this->engines as $engine) {
      if($engine->isAvailable()) {
        $this->currentEngine = $engine;
        return $engine;
      }
    }
    throw new RuntimeException("No code coverage engine is available.");
  }
}
?>