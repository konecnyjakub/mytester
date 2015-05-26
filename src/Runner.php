<?php
namespace MyTester;

/**
 * Description of Runner
 *
 * @author Jakub Konečný
 */
class Runner {
  private $name;
  private $jobs = array();
  function __construct($name) {
    if(is_string($name)) $this->name = $name;
  }
  
  function addJob($name, $callback) {
    $job = new Job($name, $callback);
    $count = count($this->jobs);
    $this->jobs[$count] = $job;
    $return = & $this->jobs[$count];
    return $return;
  }
  
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