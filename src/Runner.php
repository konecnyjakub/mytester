<?php
namespace MyTester;

/**
 * Runner for test suit
 *
 * @author Jakub Konečný
 */
class Runner {
  /** @var string Name of the runner */
  protected $name;
  /** @var array */
  protected $jobs = array();
  
  /**
   * @param string $name Name of the runner
   */
  function __construct($name) {
    if(is_string($name)) $this->name = $name;
  }
  
  /**
   * Adds new job to the runner
   * 
   * @param string $name Name of the job
   * @param callable $callback Task
   * @return \MyTester\Job
   */
  function addJob($name, callable $callback) {
    $job = new Job($name, $callback);
    $count = count($this->jobs);
    $this->jobs[$count] = $job;
    $return = & $this->jobs[$count];
    return $return;
  }
  
  /**
   * Executes all jobs of the runner
   * 
   * @return array Results of the test suit
   */
  function run() {
    if(!Environment::isSetUp()) {
      echo "Warrning: Testing Environment is not set. Setting up ...\n";
      Environment::setup();
    }
    Environment::resetCounter();
    $output = "***Starting suit $this->name***\n";
    foreach($this->jobs as $job) {
      $result = $job->execute();
      $output .= $result;
    }
    $testsPassed = substr_count($output, " passed. ");
    $testsFailed = substr_count($output, " failed. ");
    $testsTotal = $testsPassed + $testsFailed;
    $output .= "Executed $testsTotal tests in total. $testsPassed passed, $testsFailed failed.\n";
    $output .= "***Finished suit $this->name***\n";
    return $output;
  }
}
?>