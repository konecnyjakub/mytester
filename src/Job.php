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
  
  /**
   * @param string $name Name of the job
   * @param callable $callback The task
   */
  function __construct($name, callable $callback) {
    if(is_string($name)) $this->name = $name;
    $this->callback = $callback;
  }
  
  /**
   * Executes the task
   * 
   * @return array Results of the test
   */
  function execute() {
    Environment::resetCounter();
    ob_start();
    echo "****Starting job $this->name****\n";
    if(isset($this->callback)) call_user_func($this->callback);
    $output = ob_get_contents();
    $testsPassed = substr_count($output, " passed. ");
    $testsFailed = substr_count($output, " failed. ");
    ob_clean();
    $testsTotal = Environment::$taskCount;
    echo "****Finished job $this->name****\n";
    echo "Executed $testsTotal tests. $testsPassed passed, $testsFailed failed.\n";
    $output .= ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $output;
  }
}
