<?php
namespace MyTester;

/**
 * One test suit
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2016, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class TestCase {
  use \Nette\SmartObject;
  
  const METHOD_PATTERN = '#^test[A-Z0-9_]#';
  
  /**
   * Check whetever to skip a test method
   * 
   * @param \Nette\Reflection\Method $method
   * @return bool
   */
  protected function checkSkip(\Nette\Reflection\Method $method) {
    if(!$method->hasAnnotation("skip")) return false;
    $value = $method->getAnnotation("skip");
    if(is_string($value) OR is_int($value) OR is_float($value) OR is_bool($value)) {
      return (bool) $value;
    } elseif($value instanceof \Nette\Utils\ArrayHash) {
      foreach($value as $k => $v) {
        if($k === "php") {
          return version_compare(PHP_VERSION, $v, "<");
        }
      }
    }
    return false;
  }
  
  /**
   * Get list of jobs with parameters for current test suit
   * 
   * @return array
   */
  protected function getJobs() {
    $jobs = array();
    $r = new \Nette\Reflection\ClassType(get_class($this));
    $methods = array_values(preg_grep(static::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $rm = $r->getMethod($method);
      $data = NULL;
      $job = array(
        "name" => $this->getJobName($rm), "callback" => array($this, $method), "params" => NULL, "skip" => $this->checkSkip($rm)
      );
      if($rm->getNumberOfParameters() AND $rm->hasAnnotation("data")) {
        $data = (array) $rm->getAnnotation("data");
      }
      if(is_array($data)) {
        foreach($data as $value) {
          $job["params"][0] = $value;
          $jobs[] = $job;
        }
      } else {
        $jobs[] = $job;
      }
    }
    return $jobs;
  }
  
  /**
   * Get name of current test suit
   * 
   * @return string
   */
  protected function getSuitName() {
    $suitName = get_class($this);
    $r = new \Nette\Reflection\ClassType($suitName);
    if($r->hasAnnotation("testSuit")) $suitName = (string) $r->getAnnotation("testSuit");
    return $suitName;
  }
  
  /**
   * Get name for a job
   * 
   * @param \Nette\Reflection\Method $method
   * @return string
   */
  protected function getJobName(\Nette\Reflection\Method $method) {
    if($method->hasAnnotation("test")) return (string) $method->getAnnotation("test");
    else return $this->getSuitName() . "::" . $method->getName();
  }
  
  /**
   * Runs the test suit
   * 
   * @return void
   */
  function run() {
    $suitName = $this->getSuitName();
    $runner = new Runner($suitName);
    $jobs = $this->getJobs();
    foreach($jobs as $job) {
      $runner->addJob($job["name"], $job["callback"], $job["params"], $job["skip"]);
    }
    $output = $runner->run();
    if(Environment::getOutput() == "screen") {
      echo $output;
    } else {
      $time = date("o-m-d-h-i-s");
      $filename = "../$suitName-$time.log";
      Environment::printLine("Trying to create file $filename ...", true);
      if(file_put_contents($filename, $output)) {
        Environment::printLine("Successfuly created.", true);
      } else {
        Environment::printLine("An error occurred.", true);
      }
    }
  }
}
?>