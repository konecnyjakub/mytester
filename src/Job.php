<?php
namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 */
class Job {
  /** @var string Name of the job */
  protected $name;
  /** @var callable Task */
  protected $callback;
  /** @var array Results of the task */
  protected $output = array();
  
  /**
   * @param string $name Name of the job
   * @param callable $callback The task
   */
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
  protected function write($text) {
    $this->output[] = $text;
  }
  
  /**
   * Executes the task
   * 
   * @return array Results of the test
   */
  function execute() {
    Environment::resetCounter();
    ob_start(/*array($this, "write")*/);
    echo "*****Starting job $this->name*****\n";
    if(isset($this->callback)) call_user_func($this->callback);
    $output = ob_get_contents();
    $testsPassed = substr_count($output, " passed. ");
    $testsFailed = substr_count($output, " failed. ");
    ob_clean();
    $testsTotal = Environment::$taskCount;
    echo "Executed $testsTotal tests. $testsPassed passed, $testsFailed failed.\n";
    echo "*****Finished job $this->name*****\n";
    $output .= ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $output;
  }
}
