<?php
declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;
use TypeError;

/**
 * One job of a test suite
 *
 * @author Jakub Konečný
 * @property-read callable $callback
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

    /** @var callable Task */
    private $callback;
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
     * @param mixed[] $params
     * @param callable[] $onAfterExecute
     */
    public function __construct(
        public readonly string $name,
        callable $callback,
        public readonly array $params = [],
        private bool|string $skip = false,
        public array $onAfterExecute = [],
        public readonly string $dataSetName = "",
        public readonly bool $reportDeprecations = true
    ) {
        if (count($this->onAfterExecute) > 0) {
            trigger_error("Using " . self::class . "::]\$onAfterExecute is deprecated, add a listener for event " . Events\TestJobFinished::class . " instead", E_USER_DEPRECATED);
        }
        $this->callback = $callback;
    }

    protected function getCallback(): callable
    {
        return $this->callback;
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
     * @deprecated
     */
    private function onAfterExecute(): void
    {
        foreach ($this->onAfterExecute as $callback) {
            $callback($this);
        }
    }

    /**
     * Executes the task
     */
    public function execute(): void
    {
        if ($this->skip === false) {
            $timerName = $this->name . time();
            Timer::start($timerName);
            ob_start();
            set_error_handler(
                function (int $errno, string $errstr, string $errfile, int $errline): bool {
                    if ($this->reportDeprecations) {
                        $this->eventDispatcher->dispatch(new Events\DeprecationTriggered($errstr, $errfile, $errline));
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
                        isset($e->getTrace()[0]["class"]) && $e->getTrace()[0]["class"] === TestCase::class &&
                        isset($e->getTrace()[0]["function"]) && str_starts_with($e->getTrace()[0]["function"], "assert")
                    ) {
                        /** @var array{0: TestCase, 1: string}&callable $callback */
                        $callback = $this->callback;
                        throw new AssertionFailedException(
                            "Invalid value passed to an assertion.",
                            $callback[0]->getCounter() + 1,
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
            if (is_array($this->callback) && isset($this->callback[0]) && $this->callback[0] instanceof TestCase) {
                $this->totalAssertions = $this->callback[0]->getCounter();
            }
            $this->eventDispatcher->dispatch(new Events\TestJobFinished($this));
            $this->onAfterExecute(); // @phpstan-ignore method.deprecated
            restore_error_handler();
            $this->output = (string) ob_get_clean();
            Timer::stop($timerName);
            // @phpstan-ignore argument.type, cast.int
            $this->totalTime = (int) Timer::read($timerName, Timer::FORMAT_PRECISE);
        }
        $this->result = JobResult::fromJob($this);
    }
}
