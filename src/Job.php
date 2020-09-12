<?php
declare(strict_types=1);

namespace MyTester;

require_once __DIR__ . "/functions.php";

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @property-read callable $callback
 * @property-read bool|string $skip
 * @property-read string $result
 */
class Job {
  use \Nette\SmartObject;

  protected string $name;
  /** @var callable Task */
  protected $callback;
  protected array $params = [];
  /** @var bool|string */
  protected $skip;
  protected bool $shouldFail;
  protected string $result = "passed";
  
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
  
  protected function getCallback(): callable {
    return $this->callback;
  }
  
  /**
   * @return bool|string
   */
  protected function getSkip() {
    return $this->skip;
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
      $this->result = "skipped";
      Environment::addSkipped($this->name, (!is_bool($this->skip) ? $this->skip : ""));
    } else {
      ob_start();
      if(isset($this->callback)) {
        call_user_func_array($this->callback, $this->params);
      }
      /** @var string $output */
      $output = ob_get_clean();
      $failed = Environment::checkFailed($output);
      if($failed && !$this->shouldFail) {
        $this->result = "failed";
      }
      if(strlen($output) && $this->result === "failed") {
        file_put_contents(\getTestsDirectory() . "/$this->name.errors", $output);
      }
    }
    Environment::setShouldFail(false);
  }
}
?>