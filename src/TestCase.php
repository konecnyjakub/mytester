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
    $className = get_class($this);
    $jobs = array();
    $r = new \Nette\Reflection\ClassType($className);
    $methods = array_values(preg_grep(self::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $params = $r->getMethod($method)->getParameters();
      $job = array(
        "name" => "$className::$method", "callback" => array($this, $method), "params" => NULL
      );
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
   * Runs the test suit
   * 
   * @return void
   */
  function run() {
    $className = get_class($this);
    $runner = new Runner($className);
    $jobs = $this->getJobs();
    foreach($jobs as $job) {
      $runner->addJob($job["name"], $job["callback"], $job["params"]);
    }
    $output = $runner->run();
    if(Environment::$output == "screen") {
      echo $output;
    } else {
      $time = date("o-m-d-h-i-s");
      $filename = "../$className-$time.log";
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