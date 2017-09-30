<?php
namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2017, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 * @property-read bool $skip
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
  
  /**
   * @param string $name Name of the job
   * @param callable $callback The task
   * @param array $params Additional parameters for the job   
   * @param bool $skip
   */
  public function __construct($name, callable $callback, $params = "", $skip = false) {
    $this->name = (string) $name;
    $this->callback = $callback;
    if(is_array($params)) {
      $this->params = $params;
    }
    $this->skip = (bool) $skip;
  }
  
  /**
   * @return bool
   */
  public function getSkip() {
    return $this->skip;
  }
  
  /**
   * Executes the task
   * 
   * @return array Results of the test
   */
  public function execute() {
    \Tracy\Debugger::timer($this->name);
    Environment::resetCounter();
    $output  = "";
    ob_start();
    if($this->skip) {
      Environment::printLine("****Skipping job $this->name****");
    } else {
      Environment::printLine("****Starting job $this->name****");
      if(isset($this->callback)) {
        call_user_func_array($this->callback, $this->params);
      }
      $output .= ob_get_contents();
      ob_clean();
      ob_start();
      Environment::printLine("****Finished job $this->name****");
      Environment::testStats($output, $this->name);
    }
    $output .= ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $output;
  }
}
?>