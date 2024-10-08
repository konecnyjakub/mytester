<?php
declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @property-read callable $callback
 * @property-read bool|string $skip
 * @property-read JobResult $result
 * @property-read string $output @internal
 * @property-read int $totalTime Total elapsed time in milliseconds
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

    /**
     * @param callable[] $onAfterExecute
     */
    public function __construct(
        public readonly string $name,
        callable $callback,
        public readonly array $params = [],
        private bool|string $skip = false,
        public array $onAfterExecute = []
    ) {
        $this->callback = $callback;
    }

    protected function getCallback(): callable
    {
        return $this->callback;
    }

    public function getSkip(): bool|string
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

    private function onAfterExecute(): void
    {
        foreach ($this->onAfterExecute as $callback) {
            $callback();
        }
    }

    /**
     * Executes the task
     */
    public function execute(): void
    {
        if (!$this->skip) {
            $timerName = $this->name . time();
            Timer::start($timerName);
            ob_start();
            try {
                call_user_func_array($this->callback, $this->params);
            } catch (SkippedTestException $e) {
                $this->skip = ($e->getMessage() !== "") ? $e->getMessage() : true;
            } catch (IncompleteTestException $e) {
                $message = $e->getMessage() !== "" ? $e->getMessage() : "incomplete";
                echo "Warning: $message\n";
            } catch (AssertionFailedException $e) {
                echo $e->getMessage();
            }
            $this->onAfterExecute();
            /** @var string $output */
            $output = ob_get_clean();
            $this->output = $output;
            Timer::stop($timerName);
            // @phpstan-ignore argument.type
            $this->totalTime = (int) Timer::read($timerName, Timer::FORMAT_PRECISE);
        }
        $this->result = JobResult::fromJob($this);
    }
}
