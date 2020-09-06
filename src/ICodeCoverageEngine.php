<?php
declare(strict_types=1);

namespace MyTester;

interface ICodeCoverageEngine {
  public function getName(): string;
  public function isAvailable(): bool;
  public function start(): void;
  public function collect(): array;
}
?>