<?php
namespace MyTester;

/**
 * Runner for test suit
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
class Runner extends \Nette\Object {
  /** @var string Name of the runner */
  protected $name;
  /** @var Job[] */
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
   * @param \callable $callback Task
   * @param array $params Additional parameters for job
   * @param bool $skip
   * @return \MyTester\Job
   */
  function addJob($name, callable $callback, $params = NULL, $skip = false) {
    $job = new Job($name, $callback, $params, $skip);
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
      Environment::printLine("Warning: Testing Environment is not set. Setting up ...");
      Environment::setup();
    }
    Environment::printLine("**Starting suit $this->name**");
    $output = ob_get_contents();
    ob_clean();
    foreach($this->jobs as $job) {
      $output .= $job->execute();
    }
    ob_start();
    Environment::printLine("**Finished suit $this->name**");
    $time_end = microtime(true);
    Environment::testStats($output, $time_start, $time_end);
    $output .= ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $output;
  }
}
?>