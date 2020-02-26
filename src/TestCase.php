<?php
declare(strict_types=1);

namespace MyTester;

/**
 * One test suit
 *
 * @author Jakub Konečný
 */
abstract class TestCase {
  use \Nette\SmartObject;
  
  public const METHOD_PATTERN = '#^test[A-Z0-9_]#';
  
  /**
   * Check whether to skip a test method
   *
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
      foreach($value as $k => $v) {
        switch($k) {
          case "php":
            if(version_compare(PHP_VERSION, (string) $v, "<")) {
              return "PHP version is lesser than $v";
            }
            break;
          case "extension":
            if(!extension_loaded($v)) {
              return "extension $v is not loaded";
            }
            break;
          case "sapi":
            if(PHP_SAPI != $v) {
              return "the sapi is not $v";
            }
            break;
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
  protected function getJobs(): array {
    $jobs = [];
    $r = new \Nette\Reflection\ClassType(get_class($this));
    $methods = array_values(preg_grep(static::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $rm = $r->getMethod($method);
      $data = [];
      /** @var callable $callback */
      $callback = [$this, $method];
      $job = [
        "name" => $this->getJobName($rm), "callback" => $callback, "params" => [], "skip" => $this->checkSkip($rm), "shouldFail" => $rm->hasAnnotation("fail")
      ];
      if($rm->getNumberOfParameters() && $rm->hasAnnotation("data")) {
        $data = (array) $rm->getAnnotation("data");
      }
      if(count($data) > 0) {
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
   */
  protected function getSuitName(): string {
    $suitName = get_class($this);
    $r = new \Nette\Reflection\ClassType($suitName);
    if($r->hasAnnotation("testSuit")) {
      $suitName = (string) $r->getAnnotation("testSuit");
    }
    return $suitName;
  }
  
  /**
   * Get name for a job
   */
  protected function getJobName(\Nette\Reflection\Method $method): string {
    if($method->hasAnnotation("test")) {
      return (string) $method->getAnnotation("test");
    }
    return $this->getSuitName() . "::" . $method->getName();
  }
  
  /**
   * Called at start of the suit
   * 
   * @return void
   */
  public function startUp() {
  }
  
  /**
   * Called at end of the suit
   * 
   * @return void
   */
  public function shutDown() {
  }
  
  /**
   * Called before each job
   * 
   * @return void
   */
  public function setUp() {
  }
  
  /**
   * Called after each job
   * 
   * @return void
   */
  public function tearDown() {
  }
  
  protected function runJob(Job $job): string {
    /** @var array $callback */
    $callback = $job->callback;
    $jobName = $this->getJobName(\Nette\Reflection\Method::from($callback[0], $callback[1]));
    Environment::$currentJob = $jobName;
    if(!$job->skip) {
      $this->setUp();
    }
    $job->execute();
    if(!$job->skip) {
      $this->tearDown();
    }
    Environment::$currentJob = "";
    switch($job->result) {
      case "passed":
        return ".";
      case "skipped":
        return "s";
      case "failed":
        return "F";
    }
    return "";
  }
  
  /**
   * Runs the test suit
   */
  public function run(): bool {
    $this->startUp();
    $jobs = $this->getJobs();
    $output = "";
    $passed = true;
    foreach($jobs as $job) {
      $output .= $this->runJob($job);
      if($job->result === "failed") {
        $passed = false;
      }
    }
    $this->shutDown();
    echo $output;
    return $passed;
  }
}
?>