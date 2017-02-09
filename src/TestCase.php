<?php
declare(strict_types=1);

namespace MyTester;

/**
 * One test suit
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2017, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class TestCase {
  use \Nette\SmartObject;
  
  const METHOD_PATTERN = '#^test[A-Z0-9_]#';
  
  /**
   * Check whether to skip a test method
   * 
   * @param \Nette\Reflection\Method $method
   * @return bool|string
   */
  protected function checkSkip(\Nette\Reflection\Method $method) {
    if(!$method->hasAnnotation("skip")) {
      return false;
    }
    $value = $method->getAnnotation("skip");
    if(is_scalar($value)) {
      return (bool) $value;
    } elseif($value instanceof \Nette\Utils\ArrayHash) {
      $skip = false;
      $reason = "";
      foreach($value as $k => $v) {
        switch ($k) {
          case "php":
            $skip = version_compare(PHP_VERSION, (string) $v, "<");
            if($skip) {
              $reason = "PHP version is lesser than $v";
            }
            break;
          case "extension":
            $skip = !extension_loaded($v);
            if($skip) {
              $reason = "extension $v is not loaded";
            }
            break;
          case "sapi":
            $skip = PHP_SAPI != $v;
            if($skip) {
              $reason = "the sapi is not $v";
            }
            break;
        }
        if($skip) {
          return $reason;
        }
      }
    }
    return false;
  }
  
  /**
   * Get list of jobs with parameters for current test suit
   * 
   * @return Job[]
   */
  protected function getJobs() {
    $jobs = [];
    $r = new \Nette\Reflection\ClassType(get_class($this));
    $methods = array_values(preg_grep(static::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $rm = $r->getMethod($method);
      $data = NULL;
      $job = [
        "name" => $this->getJobName($rm), "callback" => [$this, $method], "params" => [], "skip" => $this->checkSkip($rm), "shouldFail" => $rm->hasAnnotation("fail")
      ];
      if($rm->getNumberOfParameters() AND $rm->hasAnnotation("data")) {
        $data = (array) $rm->getAnnotation("data");
      }
      if(is_array($data)) {
        foreach($data as $value) {
          $job["params"][0] = $value;
          $jobs[] = new Job($job["name"], $job["callback"], $job["params"], $job["skip"], $job["shouldFail"]);
          $job["params"] = [];
        }
      } else {
        $jobs[] = new Job($job["name"], $job["callback"], $job["params"], $job["skip"], $job["shouldFail"]); 
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
    if($r->hasAnnotation("testSuit")) {
      $suitName = (string) $r->getAnnotation("testSuit");
    }
    return $suitName;
  }
  
  /**
   * Get name for a job
   * 
   * @param \Nette\Reflection\Method $method
   * @return string
   */
  protected function getJobName(\Nette\Reflection\Method $method) {
    if($method->hasAnnotation("test")) {
      return (string) $method->getAnnotation("test");
    } else {
      return $this->getSuitName() . "::" . $method->getName();
    }
  }
  
  /**
   * Called at start of the suit
   * 
   * @return void
   */
  function startUp() {
    
  }
  
  /**
   * Called at end of the suit
   * 
   * @return void
   */
  function shutDown() {
    
  }
  
  /**
   * Called before each job
   * 
   * @return void
   */
  function setUp() {
    
  }
  
  /**
   * Called after each job
   * 
   * @return void
   */
  function tearDown() {
    
  }
  
  /**
   * @param \MyTester\Job $job
   * @return string
   */
  protected function runJob(Job $job) {
    $jobName = $this->getJobName(\Nette\Reflection\Method::from($job->callback[0], $job->callback[1]));
    Environment::$currentJob = $jobName;
    if(!$job->skip) {
      $this->setUp();
    }
    $job->execute();
    if(!$job->skip) {
      $this->tearDown();
    }
    Environment::$currentJob = "";
    switch ($job->result) {
      case "passed":
        return ".";
        break;
      case "skipped":
        return "s";
        break;
      case "failed":
        return "F";
        break;
    }
    return "";
  }
  
  /**
   * Runs the test suit
   * 
   * @return bool
   */
  function run() {
    $this->startUp();
    $jobs = $this->getJobs();
    $output = "";
    $passed = true;
    foreach($jobs as $job) {
      $output .= $this->runJob($job);
      if($job->result === "failed")  {
        $passed = false;
      }
    }
    $this->shutDown();
    echo $output;
    return $passed;
  }
}
?>