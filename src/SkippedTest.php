<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Skipped test info
 *
 * @author Jakub Konečný
 * @internal
 * @property string|bool $reason
 */
final class SkippedTest {
  use \Nette\SmartObject;

  public string $name;
  /** @var string|bool */
  private $reason;

  /**
   * @param bool|string $reason
   */
  public function __construct(string $name, $reason) {
    $this->name = $name;
    $this->reason = $reason;
  }

  /**
   * @return bool|string
   */
  protected function getReason() {
    return $this->reason;
  }

  /**
   * @param string|bool $reason
   */
  protected function setReason($reason): void {
    $this->reason = $reason;
  }
}
?>