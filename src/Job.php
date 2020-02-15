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
  
  /** @var string Name of the job */
  protected $name;
  /** @var callable Task */
  protected $callback;
  /** @var array Additional parameters */
  protected $params = [];
  /** @var bool|string */
  protected $skip;
  /** @var bool */
  protected $shouldFail;
  /** @var string */
  protected $result = "passed";
  
  /**
   * @param string $name Name of the job
   * @param callable $callback The task
   * @param array $params Additional parameters for the job
   * @param bool|string $skip
   * @param bool $shouldFail
   */
  public function __construct(string $name, callable $callback, array $params = [], $skip = false, bool $shouldFail = false) {
    $this->name = $name;
    $this->callback = $callback;
    $this->params = $params;
    $this->skip = $skip;
    $this->shouldFail = $shouldFail;
  }
  
  public function getCallback(): callable {
    return $this->callback;
  }
  
  /**
   * @return bool|string
   */
  public function getSkip() {
    return $this->skip;
  }
  
  public function getResult(): string {
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