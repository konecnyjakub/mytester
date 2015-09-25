<?php
namespace MyTester;

/**
 * One test suit
 *
 * @author Jakub Konečný
 * @copyright (c) 2015, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class TestCase {
  const METHOD_PATTERN = '#^test[A-Z0-9_]#';
  
  /**
   * Get list of jobs with parameters for current test suit
   * 
   * @return array
   */
  protected function getJobs() {
    $jobs = array();
    $r = new \Nette\Reflection\ClassType(get_class($this));
    $methods = array_values(preg_grep(self::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $rm = $r->getMethod($method);
      $params = $rm->getParameters();
      $job = array(
        "name" => $this->getSuitName() . "::$method", "callback" => array($this, $method), "params" => NULL, "skip" => false
      );
      if($rm->hasAnnotation("test")) $job["name"] = (string) $rm->getAnnotation("test");
      if($rm->hasAnnotation("skip")) $job["skip"] = true;
      if(count($params) > 0) {
        foreach($params as $param) {
          $paramName = $param->getName();
          global $$paramName;
          $job["params"][] = $$paramName;
        }
      }
      $jobs[] = $job;
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
    return $suitName;
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
    if(Environment::$output == "screen") {
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