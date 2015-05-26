<?php
namespace MyTester;

/**
 * Description of Job
 *
 * @author Jakub Konečný
 */
class Job {
  private $name;
  private $callback;
  private $output;
  function __construct($name, $callback) {
    if(is_string($name)) $this->name = $name;
    if(is_callable($callback)) { $this->callback = $callback; }
  }
  
  private function write($text) {
    $this->output[] = $text;
  }
  
  function execute() {
    ob_start(array($this, "write"));
    echo "*****Starting job $this->name*****\n";
    if(isset($this->callback)) call_user_func($this->callback);
    echo "*****Finished job $this->name*****\n";
    ob_end_flush();
    return $this->output;
  }
}
