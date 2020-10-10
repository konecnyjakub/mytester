<?php

declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\NetteReflectionEngine;
use MyTester\Annotations\PhpAttributesEngine;
use MyTester\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;

/**
 * One test suite
 *
 * @author Jakub Konečný
 * @property-read Job[] $jobs @internal
 */
abstract class TestCase
{
    use \Nette\SmartObject;
    use TAssertions;

    public const RESULT_PASSED = ".";
    public const RESULT_SKIPPED = "s";
    public const RESULT_FAILED = "F";

    public const METHOD_PATTERN = '#^test[A-Z0-9_]#';

    /** @internal */
    public const ANNOTATION_TEST = "test";
    /** @internal */
    public const ANNOTATION_TEST_SUITE = "testSuite";

    protected SkipChecker $skipChecker;
    protected ShouldFailChecker $shouldFailChecker;
    protected DataProvider $dataProvider;
    protected Reader $annotationsReader;

    public function __construct()
    {
        $this->annotationsReader = new Reader();
        $this->annotationsReader->registerEngine(new PhpAttributesEngine());
        $this->annotationsReader->registerEngine(new NetteReflectionEngine());
        $this->skipChecker = new SkipChecker($this->annotationsReader);
        $this->shouldFailChecker = new ShouldFailChecker($this->annotationsReader);
        $this->dataProvider = new DataProvider($this->annotationsReader);
    }

    /**
     * Get list of jobs with parameters for current test suite
     *
     * @return Job[]
     */
    protected function getJobs(): array
    {
        static $jobs = [];
        if (count($jobs) === 0) {
            $r = new ReflectionClass(static::class);
            $methods = array_values(preg_grep(static::METHOD_PATTERN, array_map(function (ReflectionMethod $rm) {
                return $rm->getName();
            }, $r->getMethods())));
            foreach ($methods as $method) {
                $reflection = new ReflectionMethod(static::class, $method);
                if (!$reflection->isPublic()) {
                    continue;
                }
                /** @var callable $callback */
                $callback = [$this, $method];
                $job = [
                    "name" => $this->getJobName(static::class, $method),
                    "callback" => $callback,
                    "params" => [],
                    "skip" => $this->skipChecker->shouldSkip(static::class, $method),
                    "shouldFail" => $this->shouldFailChecker->shouldFail(static::class, $method),
                ];
                $data = $this->dataProvider->getData($this, $method);
                if (count($data) > 0) {
                    foreach ($data as $value) {
                        $job["params"][0] = $value;
                        $jobs[] = new Job(... $job);
                        $job["params"] = [];
                    }
                } else {
                    $jobs[] = new Job($job["name"], $job["callback"], $job["params"], $job["skip"], $job["shouldFail"]);
                }
            }
        }
        return $jobs;
    }

    /**
     * Get name of current test suite
     */
    protected function getSuiteName(): string
    {
        $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST_SUITE, static::class);
        if ($annotation !== null) {
            return $annotation;
        }
        return static::class;
    }

    /**
     * Get name for a job
     * @param string|object $class
     */
    protected function getJobName($class, string $method): string
    {
        $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST, $class, $method);
        if ($annotation !== null) {
            return $annotation;
        }
        return $this->getSuiteName() . "::" . $method;
    }

    /**
     * Called at start of the suite
     */
    public function startUp(): void
    {
    }

    /**
     * Called at end of the suite
     */
    public function shutDown(): void
    {
    }

    /**
     * Called before each job
     */
    public function setUp(): void
    {
    }

    /**
     * Called after each job
     */
    public function tearDown(): void
    {
    }

    protected function runJob(Job $job): string
    {
        $this->resetCounter();
        if (!$job->skip) {
            $this->setUp();
        }
        $this->shouldFail = $job->shouldFail;
        $job->execute();
        if (!$job->skip) {
            $this->tearDown();
        }
        $this->shouldFail = false;
        $this->resetCounter();
        switch ($job->result) {
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
     * Runs the test suite
     */
    public function run(): bool
    {
        $this->startUp();
        $jobs = $this->getJobs();
        $passed = true;
        foreach ($jobs as $job) {
            $this->runJob($job);
            if ($job->result === Job::RESULT_FAILED) {
                $passed = false;
            }
        }
        $this->shutDown();
        return $passed;
    }
}
