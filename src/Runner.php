<?php
namespace MyTester;

/**
 * Runner for test suit
 *
 * @author Jakub Konečný
 * @copyright (c) 2015, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
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
   * @param array $params Additional parameters for job
   * @return \MyTester\Job
   */
  function addJob($name, callable $callback, $params = array()) {
    if(is_array($params) AND count($params) > 0) $job = new Job($name, $callback, $params);
    else $job = new Job($name, $callback);
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
    $time_start = microtime(true);
    ob_start();
    if(!Environment::isSetUp()) {
      Environment::printLine("Warrning: Testing Environment is not set. Setting up ...");
      Environment::setup();
    }
    Environment::resetCounter();
    Environment::printLine("**Starting suit $this->name**");
    $output = ob_get_contents();
    ob_clean();
    foreach($this->jobs as $job) {
      $result = $job->execute();
      $output .= $result;
    }
    $testsPassed = substr_count($output, " passed. ");
    $testsFailed = substr_count($output, " failed. ");
    $testsTotal = $testsPassed + $testsFailed;
    ob_start();
    Environment::printLine("**Finished suit $this->name**");
    Environment::printLine("Executed $testsTotal tests in total. $testsPassed passed, $testsFailed failed.");
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    Environment::printLine("Execution time: $time second(s)");
    $output .= ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $output;
  }
}
?>