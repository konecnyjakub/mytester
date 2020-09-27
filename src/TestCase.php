<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\NetteReflectionEngine;
use MyTester\Annotations\PhpAttributesEngine;
use MyTester\Annotations\Reader;
use ReflectionClass;

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

  /** @internal */
  public const ANNOTATION_TEST = "test";
  /** @internal */
  public const ANNOTATION_TEST_SUIT = "testSuit";

  protected SkipChecker $skipChecker;
  protected ShouldFailChecker $shouldFailChecker;
  protected DataProvider $dataProvider;
  protected Reader $annotationsReader;

  public function __construct() {
    $this->annotationsReader = new Reader();
    $this->annotationsReader->registerEngine(new PhpAttributesEngine());
    $this->annotationsReader->registerEngine(new NetteReflectionEngine());
    $this->skipChecker = new SkipChecker($this->annotationsReader);
    $this->shouldFailChecker = new ShouldFailChecker($this->annotationsReader);
    $this->dataProvider = new DataProvider($this->annotationsReader);
  }
  
  /**
   * Get list of jobs with parameters for current test suit
   * 
   * @return Job[]
   */
  protected function getJobs(): array {
    $jobs = [];
    $r = new ReflectionClass(static::class);
    $methods = array_values(preg_grep(static::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      /** @var callable $callback */
      $callback = [$this, $method];
      $job = [
        "name" => $this->getJobName(static::class, $method),
        "callback" => $callback,
        "params" => [],
        "skip" => $this->skipChecker->shouldSkip(static::class, $method),
        "shouldFail" => $this->shouldFailChecker->shouldFail(static::class, $method),
      ];
      $data = $this->dataProvider->getData(static::class, $method);
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
    $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST_SUIT, static::class);
    if($annotation !== null) {
      return $annotation;
    }
    return static::class;
  }
  
  /**
   * Get name for a job
   * @param string|object $class
   */
  protected function getJobName($class, string $method): string {
    $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST, $class, $method);
    if($annotation !== null) {
      return $annotation;
    }
    return $this->getSuitName() . "::" . $method;
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
    $jobName = $this->getJobName($callback[0], $callback[1]);
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