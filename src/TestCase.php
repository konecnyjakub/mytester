<?php
declare(strict_types=1);

namespace MyTester;

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

    public const string METHOD_PATTERN = '#^test[A-Z0-9_]#';

    /** @internal */
    public const string ANNOTATION_TEST = "test";
    /** @internal */
    public const string ANNOTATION_TEST_SUITE = "testSuite";

    protected ISkipChecker $skipChecker;
    protected IDataProvider $dataProvider;
    protected Reader $annotationsReader;

    /** @var Job[] */
    private array $jobs = [];

    public function __construct()
    {
        $this->annotationsReader = new Reader();
        $this->annotationsReader->registerEngine(new PhpAttributesEngine());
        $this->skipChecker = new SkipChecker($this->annotationsReader);
        $this->dataProvider = new DataProvider($this->annotationsReader);
    }

    /**
     * Get list of test methods in current test suite
     *
     * @return string[]
     */
    protected function getTestMethodsNames(): array
    {
        $r = new ReflectionClass(static::class);
        return array_values(
            (array) preg_grep(
                static::METHOD_PATTERN,
                array_map(
                    function (ReflectionMethod $rm) {
                        return $rm->getName();
                    },
                    $r->getMethods(ReflectionMethod::IS_PUBLIC)
                )
            )
        );
    }

    /**
     * Get list of jobs with parameters for current test suite
     *
     * @return Job[]
     */
    protected function getJobs(): array
    {
        if (count($this->jobs) === 0) {
            $methods = $this->getTestMethodsNames();
            foreach ($methods as $method) {
                /** @var callable $callback */
                $callback = [$this, $method];
                $job = [
                    "name" => $this->getJobName(static::class, $method),
                    "callback" => $callback,
                    "params" => [],
                    "skip" => $this->skipChecker->shouldSkip(static::class, $method),
                    "onAfterExecute" => [
                        function (Job $job): void {
                            $job->totalAssertions = $this->getCounter();
                            if ($job->totalAssertions === 0) {
                                echo "Warning: No assertions were performed.\n";
                            }
                        },
                    ],
                ];
                $data = $this->dataProvider->getData($this, $method);
                if (count($data) > 0) {
                    foreach ($data as $value) {
                        $job["params"][0] = $value;
                        $this->jobs[] = new Job(... $job);
                        $job["params"] = [];
                    }
                } else {
                    $rm = new ReflectionMethod($this, $method);
                    if ($rm->getNumberOfParameters() > 0) {
                        $job["skip"] = "Method requires at least 1 parameter but data provider does not provide any.";
                    }
                    $this->jobs[] = new Job(... $job);
                }
            }
        }
        return $this->jobs;
    }

    /**
     * Get name of a test suite
     *
     * @internal
     */
    public function getSuiteName(string|object|null $class = null): string
    {
        $class = $class ?? static::class;
        $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST_SUITE, $class);
        if ($annotation !== null) {
            return $annotation;
        }
        return is_object($class) ? get_class($class) : $class;
    }

    /**
     * Get name for a job
     */
    protected function getJobName(string|object $class, string $method): string
    {
        $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST, $class, $method);
        if ($annotation !== null) {
            return $annotation;
        }
        return $this->getSuiteName($class) . "::" . $method;
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

    /**
     * Interrupts the job's run, it is reported as passed with warning
     */
    protected function markTestIncomplete(string $message = ""): void
    {
        throw new IncompleteTestException($message);
    }

    /**
     * Interrupts the job's run, it is reported as skipped
     */
    protected function markTestSkipped(string $message = ""): void
    {
        throw new SkippedTestException($message);
    }

    protected function runJob(Job $job): string
    {
        $this->resetCounter();
        if (!$job->skip) {
            $this->setUp();
        }
        $job->execute();
        if (!$job->skip) {
            $this->tearDown();
        }
        $this->resetCounter();
        return $job->result->output();
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
            $passed = $passed && $job->result !== JobResult::FAILED;
        }
        $this->shutDown();
        return $passed;
    }
}
