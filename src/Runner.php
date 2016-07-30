<?php
namespace MyTester;

/**
 * Runner for test suit
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 * @deprecated since version 1.0
 */
class Runner {
  use \Nette\SmartObject;
  
  /** @var string Name of the runner */
  protected $name;
  /** @var Job[] */
  protected $jobs = [];
  
  /**
   * @param string $name Name of the runner
   * @deprecated since version 1.0
   */
  function __construct($name) {
    if(is_string($name)) $this->name = $name;
    trigger_error(get_class() . " is now deprecated.", E_USER_DEPRECATED);
  }
  
  /**
   * Adds new job to the runner
   * 
   * @param string $name Name of the job
   * @param \callable $callback Task
   * @param array $params Additional parameters for job
   * @param bool $skip
   * @return \MyTester\Job
   * @deprecated since version 1.0
   */
  function addJob($name, callable $callback, $params = NULL, $skip = false) {
    trigger_error(get_class() . " is now deprecated.", E_USER_DEPRECATED);
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
   * @deprecated since version 1.0
   */
  function run() {
    trigger_error(get_class() . " is now deprecated.", E_USER_DEPRECATED);
    \Tracy\Debugger::timer($this->name);
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
    Environment::testStats($output, $this->name);
    $output .= ob_get_contents();
    ob_clean();
    ob_end_flush();
    return $output;
  }
}
?>