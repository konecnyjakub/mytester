<?php
namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @copyright (c) 2015, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
class Job {
  /** @var string Name of the job */
  protected $name;
  /** @var callable Task */
  protected $callback;
  /** @var array Additional parameters */
  protected $params = array();
  /** @var bool */
  protected $skip;
  
  /**
   * @param string $name Name of the job
   * @param \callable $callback The task
   * @param array $params Additional parameters for the job   
   * @param bool $skip
   */
  function __construct($name, callable $callback, $params = "", $skip = false) {
    if(is_string($name)) $this->name = $name;
    $this->callback = $callback;
    if(is_array($params)) $this->params = $params;
    $this->skip = (bool) $skip;
  }
  
  /**
   * Executes the task
   * 
   * @return array Results of the test
   */
  function execute() {
    $time_start = microtime(true);
    Environment::resetCounter();
    ob_start();
    if($this->skip) {
      Environment::printLine("****Skipping job $this->name****");
    } else {
      Environment::printLine("****Starting job $this->name****");
      if(isset($this->callback)) {
        call_user_func_array($this->callback, $this->params);
      }
      $output = ob_get_contents();
      ob_clean();
      ob_start();
      Environment::printLine("****Finished job $this->name****");
      $time_end = microtime(true);
      Environment::testStats($output, $time_start, $time_end);
    }
    $output .= ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $output;
  }
}
?>