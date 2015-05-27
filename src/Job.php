<?php
namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 */
class Job {
  /** @var string Name of the job */
  private $name;
  /** @var callable Task */
  private $callback;
  /** @var array Results of the task */
  private $output = array();
  
  function __construct($name, callable $callback) {
    if(is_string($name)) $this->name = $name;
    $this->callback = $callback;
  }
  
  /**
   * Records result of a test
   * 
   * @param string $text
   * @return void
   */
  private function write($text) {
    $this->output[] = $text;
  }
  
  /**
   * Executes the task
   * 
   * @return array Results of the test
   */
  function execute() {
    ob_start(array($this, "write"));
    echo "*****Starting job $this->name*****\n";
    if(isset($this->callback)) call_user_func($this->callback);
    echo "*****Finished job $this->name*****\n";
    ob_end_flush();
    return $this->output;
  }
}
