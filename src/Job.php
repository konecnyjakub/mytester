<?php
namespace MyTester;

require_once __DIR__ . "/functions.php";

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 * @property-read callable $callback
 * @property-read bool $skip
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
  /** @var bool */
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
  function __construct($name, callable $callback, array $params = [], $skip = false, $shouldFail = false) {
    $this->name = (string) $name;
    $this->callback = $callback;
    $this->params = $params;
    $this->skip = $skip;
    $this->shouldFail = (bool) $shouldFail;
  }
  
  /**
   * @return callable
   */
  function getCallback() {
    return $this->callback;
  }
  
  /**
   * @return bool
   */
  function getSkip() {
    return $this->skip;
  }
  
  function getResult() {
    return $this->result;
  }
  
  /**
   * Executes the task
   * 
   * @return void
   */
  function execute() {
    Environment::resetCounter();
    Environment::setShouldFail($this->shouldFail);
    $output  = "";
    ob_start();
    if($this->skip) {
      $this->result = "skipped";
      Environment::addSkipped($this->name, (!is_bool($this->skip)? $this->skip: ""));
    } else {
      if(isset($this->callback)) {
        call_user_func_array($this->callback, $this->params);
      }
      $output .= ob_get_clean();
      $failed = Environment::checkFailed($output);
      if($failed AND !$this->shouldFail) {
        $this->result = "failed";
      }
      if(strlen($output) AND $this->result === "failed") file_put_contents(\getTestsDirectory() . "/$this->name.errors", $output);
    }
    Environment::setShouldFail(false);
  }
}
?>