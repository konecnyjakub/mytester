<?php
declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;
use Closure;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionFunction;
use Throwable;
use TypeError;

/**
 * One job of a test suite
 *
 * @author Jakub Konečný
 * @property-read bool|string $skip
 * @property-read JobResult $result
 * @property-read string $output @internal
 * @property-read int $totalTime Total elapsed time in milliseconds
 * @property-read Throwable|null $exception
 * @property-read string $nameWithDataSet Job's name + data set (or its custom name)
 */
final class Job
{
    use \Nette\SmartObject;

    private JobResult $result = JobResult::PASSED;
    private string $output = "";
    /** @var int Total elapsed time in milliseconds */
    private int $totalTime = 0;
    /**
     * @internal
     */
    public int $totalAssertions = 0;
    private Throwable|null $exception = null;
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param Closure $callback Task
     * @param mixed[] $params
     */
    public function __construct(
        public readonly string $name,
        public readonly Closure $callback,
        public readonly array $params = [],
        private bool|string $skip = false,
        public readonly string $dataSetName = "",
        public readonly bool $reportDeprecations = true,
        public readonly int $maxRetries = 0
    ) {
    }

    protected function getSkip(): bool|string
    {
        return $this->skip;
    }

    protected function getResult(): JobResult
    {
        return $this->result;
    }

    protected function getOutput(): string
    {
        return $this->output;
    }

    protected function getTotalTime(): int
    {
        return $this->totalTime;
    }

    protected function getException(): ?\Throwable
    {
        return $this->exception;
    }

    protected function getNameWithDataSet(): string
    {
        $jobName = $this->name;
        if (count($this->params) > 0) {
            $jobName .= " with data set ";
            if ($this->dataSetName !== "") {
                $jobName .= $this->dataSetName;
            } else {
                $jobName .= "(" . implode(", ", $this->params) . ")";
            }
        }
        return $jobName;
    }

    /**
     * @internal
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @internal
     */
    public function getCallbackReflection(): ?ReflectionFunction
    {
        $rf = new ReflectionFunction($this->callback);
        $object = $rf->getClosureThis();
        if (!$object instanceof TestCase) {
            return null;
        }
        return $rf;
    }

    /**
     * Executes the task
     */
    public function execute(): void
    {
        for ($attemptNumber = 0; $attemptNumber <= $this->maxRetries; $attemptNumber++) {
            if ($this->skip === false) {
                $previousAttemptsAssertions = 0;
                $rm = $this->getCallbackReflection();
                /** @var TestCase|null $testCase */
                $testCase = ($rm !== null && $rm->getClosureThis() !== null) ? $rm->getClosureThis() : null;
                if ($testCase !== null) {
                    $previousAttemptsAssertions = $testCase->getCounter();
                }
                $timerName = $this->name . time();
                Timer::start($timerName);
                ob_start();
                set_error_handler(
                    function (int $errno, string $errstr, string $errfile, int $errline): bool {
                        if ($this->reportDeprecations) {
                            $this->eventDispatcher->dispatch(
                                new Events\DeprecationTriggered($errstr, $errfile, $errline)
                            );
                        }
                        return true;
                    },
                    E_USER_DEPRECATED
                );
                try {
                    try {
                        call_user_func_array($this->callback, $this->params);
                    } catch (TypeError $e) {
                        if (
                            isset($e->getTrace()[0]) &&
                            isset($e->getTrace()[0]["class"]) &&
                            $e->getTrace()[0]["class"] === TestCase::class &&
                            isset($e->getTrace()[0]["function"]) &&
                            str_starts_with($e->getTrace()[0]["function"], "assert")
                        ) {
                            throw new AssertionFailedException(
                                "Invalid value passed to an assertion.",
                                (int) $testCase?->getCounter() + 1,
                                $e
                            );
                        }
                        throw $e;
                    }
                } catch (SkippedTestException $e) {
                    $this->skip = ($e->getMessage() !== "") ? $e->getMessage() : true;
                } catch (IncompleteTestException $e) {
                    $message = $e->getMessage() !== "" ? $e->getMessage() : "incomplete";
                    echo "Warning: $message\n";
                } catch (AssertionFailedException $e) {
                    echo $e->getMessage();
                    $this->exception = $e;
                } catch (Throwable $e) {
                    echo "Error: " . ($e->getMessage() !== "" ? $e->getMessage() : $e::class) . "\n";
                    echo "Trace:\n" . $e->getTraceAsString() . "\n";
                    $this->exception = $e;
                }
                if ($testCase !== null) {
                    $this->totalAssertions = $testCase->getCounter() - $previousAttemptsAssertions;
                }
                $this->eventDispatcher->dispatch(new Events\TestJobFinished($this));
                restore_error_handler();
                $this->output = (string) ob_get_clean();
                Timer::stop($timerName);
                // @phpstan-ignore argument.type, cast.int
                $this->totalTime = (int) Timer::read($timerName, Timer::FORMAT_PRECISE);
            }
            $this->result = JobResult::fromJob($this);
            if ($this->result !== JobResult::FAILED) {
                break;
            }
        }
    }
}
