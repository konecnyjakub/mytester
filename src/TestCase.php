<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use Psr\EventDispatcher\EventDispatcherInterface;
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

    protected const string METHOD_PATTERN = '#^test[A-Z0-9_]#';

    /** @internal */
    public const string ANNOTATION_TEST = "test";
    /** @internal */
    public const string ANNOTATION_TEST_SUITE = "testSuite";
    /** @internal */
    public const string ANNOTATION_IGNORE_DEPRECATIONS = "ignoreDeprecations";
    /** @internal */
    public const string ANNOTATION_NO_ASSERTIONS = "noAssertions";

    protected ISkipChecker $skipChecker;
    protected IDataProvider $dataProvider;
    protected Reader $annotationsReader;

    /** @var Job[] */
    private array $jobs = [];

    private EventDispatcherInterface $eventDispatcher;

    public function __construct()
    {
        $this->annotationsReader = Reader::create();
        $this->skipChecker = new AnnotationsSkipChecker($this->annotationsReader);
        $this->dataProvider = new AnnotationsDataProvider($this->annotationsReader);
    }

    /**
     * @internal
     */
    final public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get list of test methods in current test suite
     *
     * @return string[]
     */
    protected function getTestMethodsNames(): array
    {
        $r = new ReflectionClass(static::class);
        /** @var string[] $result */
        $result = array_values(
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
        return $result;
    }

    /**
     * Get list of callbacks that should be called after a job finishes
     *
     * @return callable[]
     */
    protected function getJobAfterExecuteCallbacks(string $methodName): array
    {
        return [
            function (Job $job) use ($methodName): void {
                $job->totalAssertions = $this->getCounter();
                $checkAssertions =
                    !$this->annotationsReader->hasAnnotation(static::ANNOTATION_NO_ASSERTIONS, static::class) &&
                    !$this->annotationsReader->hasAnnotation(
                        static::ANNOTATION_NO_ASSERTIONS,
                        static::class,
                        $methodName
                    );
                if ($checkAssertions && $job->totalAssertions === 0) {
                    echo "Warning: No assertions were performed.\n";
                }
            },
        ];
    }

    protected function shouldReportDeprecations(string $methodName): bool
    {
        $reportDeprecationsClass = !$this->annotationsReader->hasAnnotation(
            static::ANNOTATION_IGNORE_DEPRECATIONS,
            static::class
        );
        $reportDeprecationsMethod = !$this->annotationsReader->hasAnnotation(
            static::ANNOTATION_IGNORE_DEPRECATIONS,
            static::class,
            $methodName
        );
        return $reportDeprecationsClass && $reportDeprecationsMethod;
    }

    protected function shouldSkip(string $methodName): bool|string
    {
        return $this->skipChecker->shouldSkip(static::class, $methodName);
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
                    "skip" => $this->shouldSkip($method),
                    "onAfterExecute" => $this->getJobAfterExecuteCallbacks($method),
                    "dataSetName" => "",
                    "reportDeprecations" => $this->shouldReportDeprecations($method),
                ];

                $requiredParameters = (new ReflectionMethod($this, $method))->getNumberOfParameters();
                if ($requiredParameters === 0) {
                    $this->jobs[] = new Job(... $job);
                    continue;
                }

                $data = $this->dataProvider->getData($this, $method);
                if (!is_array($data)) {
                    $data = iterator_to_array($data);
                }
                if (count($data) === 0) {
                    $job["skip"] = "Method requires at least 1 parameter but data provider does not provide any.";
                    $this->jobs[] = new Job(... $job);
                    continue;
                }

                foreach ($data as $dataSetName => $value) {
                    if (!is_array($value) || count($value) < $requiredParameters) {
                        $job["skip"] = sprintf(
                            "Method requires at least %d parameter(s) but data provider provides only %d.",
                            $requiredParameters,
                            is_array($value) ? count($value) : 0
                        );
                        $this->jobs[] = new Job(... $job);
                        $job["params"] = [];
                        break;
                    } else {
                        $job["params"] = $value;
                    }
                    if (is_string($dataSetName)) {
                        $job["dataSetName"] = $dataSetName;
                    }
                    $this->jobs[] = new Job(... $job);
                    $job["params"] = [];
                    $job["dataSetName"] = "";
                }
            }
        }

        foreach ($this->jobs as $job) {
            $job->setEventDispatcher($this->eventDispatcher);
        }

        return $this->jobs;
    }

    /**
     * Get name of a test suite
     *
     * @param class-string|object $class
     * @internal
     */
    public function getSuiteName(string|object|null $class = null): string
    {
        $class = $class ?? static::class;
        /** @var string|null $annotation */
        $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST_SUITE, $class);
        if ($annotation !== null) {
            return $annotation;
        }
        return is_object($class) ? get_class($class) : $class;
    }

    /**
     * Get name for a job
     *
     * @param class-string|object $class
     */
    protected function getJobName(string|object $class, string $method): string
    {
        $annotation = $this->annotationsReader->getAnnotation(static::ANNOTATION_TEST, $class, $method);
        /** @var string|null $annotation */
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
        if ($job->skip === false) {
            $this->eventDispatcher->dispatch(new Events\TestStarted($job));
        }
        $job->execute();
        if ($job->skip === false) {
            $this->eventDispatcher->dispatch(new Events\TestFinished($job));
        }
        $this->eventDispatcher->dispatch(match ($job->result) {
            JobResult::PASSED => new Events\TestPassed($job),
            JobResult::WARNING => new Events\TestPassedWithWarning($job),
            JobResult::FAILED => new Events\TestFailed($job),
            JobResult::SKIPPED => new Events\TestSkipped($job),
        });
        $this->resetCounter();
        return $job->result->output();
    }

    /**
     * Runs the test suite
     */
    public function run(): bool
    {
        $this->eventDispatcher->dispatch(new Events\TestSuiteStarted($this));
        $jobs = $this->getJobs();
        $passed = true;
        foreach ($jobs as $job) {
            $this->runJob($job);
            $passed = $passed && $job->result !== JobResult::FAILED;
        }
        $this->eventDispatcher->dispatch(new Events\TestSuiteFinished($this));
        return $passed;
    }
}
