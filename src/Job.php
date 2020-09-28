<?php
declare(strict_types=1);

namespace MyTester;

require_once __DIR__ . "/functions.php";

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @property-read string $name
 * @property-read callable $callback
 * @property-read array $params
 * @property-read bool|string $skip
 * @property-read bool $shouldFail
 * @property-read string $result
 */
class Job {
  use \Nette\SmartObject;

  public const RESULT_PASSED = "passed";
  public const RESULT_SKIPPED = "skipped";
  public const RESULT_FAILED = "failed";

  protected string $name;
  /** @var callable Task */
  protected $callback;
  protected array $params = [];
  /** @var bool|string */
  protected $skip;
  protected bool $shouldFail;
  protected string $result = self::RESULT_PASSED;
  
  /**
   * @param bool|string $skip
   */
  public function __construct(string $name, callable $callback, array $params = [], $skip = false,
                              bool $shouldFail = false) {
    $this->name = $name;
    $this->callback = $callback;
    $this->params = $params;
    $this->skip = $skip;
    $this->shouldFail = $shouldFail;
  }

  protected function getName(): string {
    return $this->name;
  }
  
  protected function getCallback(): callable {
    return $this->callback;
  }

  protected function getParams(): array {
    return $this->params;
  }
  
  /**
   * @return bool|string
   */
  protected function getSkip() {
    return $this->skip;
  }

  protected function isShouldFail(): bool {
    return $this->shouldFail;
  }
  
  protected function getResult(): string {
    return $this->result;
  }
  
  /**
   * Executes the task
   */
  public function execute(): void {
    Environment::resetCounter();
    Environment::setShouldFail($this->shouldFail);
    if($this->skip) {
      $this->result = static::RESULT_SKIPPED;
      Environment::addSkipped($this->name, (is_string($this->skip) ? $this->skip : ""));
    } else {
      ob_start();
      if(isset($this->callback)) {
        call_user_func_array($this->callback, $this->params);
      }
      /** @var string $output */
      $output = ob_get_clean();
      $failed = str_contains($output, " failed. ");
      if($failed && !$this->shouldFail) {
        $this->result = static::RESULT_FAILED;
      }
      if(strlen($output) && $this->result === static::RESULT_FAILED) {
        file_put_contents(\getTestsDirectory() . "/$this->name.errors", $output);
      }
    }
    Environment::setShouldFail(false);
  }
}
?>