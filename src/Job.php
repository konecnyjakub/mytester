<?php
namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
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
   * @param bool $skip
   * @param bool $shouldFail
   */
  function __construct($name, callable $callback, $params = "", $skip = false, $shouldFail = false) {
    $this->name = (string) $name;
    $this->callback = $callback;
    if(is_array($params)) $this->params = $params;
    $this->skip = (bool) $skip;
    $this->shouldFail = (bool) $shouldFail;
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
   * @return array Results of the test
   */
  function execute() {
    \Tracy\Debugger::timer($this->name);
    Environment::resetCounter();
    $output  = "";
    ob_start();
    if($this->skip) {
      $this->result = "skipped";
    } else {
      if(isset($this->callback)) {
        call_user_func_array($this->callback, $this->params);
      }
      $output .= ob_get_clean();
      $failed = Environment::checkFailed($output);
      if($failed AND !$this->shouldFail) {
        $this->result = "failed";
      } elseif(!$failed AND $this->shouldFail) {
        $this->result = "failed";
      }
    }
  }
}
?>
