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
  use TAssertions;

  public const RESULT_PASSED = ".";
  public const RESULT_SKIPPED = "s";
  public const RESULT_FAILED = "F";
  
  public const METHOD_PATTERN = '#^test[A-Z0-9_]#';

  protected SkipChecker $skipChecker;
  protected ShouldFailChecker $shouldFailChecker;

  public function __construct() {
    $this->skipChecker = new SkipChecker();
    $this->shouldFailChecker = new ShouldFailChecker();
  }
  
  /**
   * Get list of jobs with parameters for current test suit
   * 
   * @return Job[]
   */
  protected function getJobs(): array {
    $jobs = [];
    $r = new \Nette\Reflection\ClassType(static::class);
    $methods = array_values(preg_grep(static::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $rm = $r->getMethod($method);
      $data = [];
      /** @var callable $callback */
      $callback = [$this, $method];
      $job = [
        "name" => $this->getJobName($rm),
        "callback" => $callback,
        "params" => [],
        "skip" => $this->skipChecker->shouldSkip(static::class, $method),
        "shouldFail" => $this->shouldFailChecker->shouldFail(static::class, $method),
      ];
      if($rm->getNumberOfParameters() && $rm->hasAnnotation("data")) {
        /** @var mixed $annotation */
        $annotation = $rm->getAnnotation("data");
        $data = (array) $annotation;
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
    $suitName = static::class;
    $r = new \Nette\Reflection\ClassType($suitName);
    if($r->hasAnnotation("testSuit")) {
      /** @var mixed $annotation */
      $annotation = $r->getAnnotation("testSuit");
      $suitName = (string) $annotation;
    }
    return $suitName;
  }
  
  /**
   * Get name for a job
   */
  protected function getJobName(\Nette\Reflection\Method $method): string {
    if($method->hasAnnotation("test")) {
      /** @var mixed $annotation */
      $annotation = $method->getAnnotation("test");
      return (string) $annotation;
    }
    return $this->getSuitName() . "::" . $method->getName();
  }
  
  /**
   * Called at start of the suit
   */
  public function startUp(): void {
  }
  
  /**
   * Called at end of the suit
   */
  public function shutDown(): void {
  }
  
  /**
   * Called before each job
   */
  public function setUp(): void {
  }
  
  /**
   * Called after each job
   */
  public function tearDown(): void {
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
      case Job::RESULT_PASSED:
        return static::RESULT_PASSED;
      case Job::RESULT_SKIPPED:
        return static::RESULT_SKIPPED;
      case Job::RESULT_FAILED:
        return static::RESULT_FAILED;
    }
    return "";
  }
  
  /**
   * Runs the test suit
   */
  public function run(): bool {
    $this->startUp();
    $jobs = $this->getJobs();
    $passed = true;
    foreach($jobs as $job) {
      $result = $this->runJob($job);
      Environment::addResult($result);
      if($job->result === Job::RESULT_FAILED) {
        $passed = false;
      }
    }
    $this->shutDown();
    return $passed;
  }
}
?>