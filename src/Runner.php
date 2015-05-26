<?php
namespace MyTester;

/**
 * Runner for test suit
 *
 * @author Jakub Konečný
 */
class Runner {
  /** @var string Name of the runner */
  private $name;
  /** @var array */
  private $jobs = array();
  
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
  function addJob($name, $callback) {
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
    $output = array();
    $output[] = "***Starting suit $this->name***\n";
    foreach($this->jobs as $job) {
      $result = $job->execute();
      $output = array_merge($output, $result);
    }
    $output[] = "***Finished suit $this->name***\n";
    return $output;
  }
}
?>