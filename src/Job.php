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
 * @method void onAfterExecute()
 */
final class Job
{
    use \Nette\SmartObject;

    public readonly string $name;
    /** @var callable Task */
    private $callback;
    public readonly array $params;
    private bool|string $skip;
    private JobResult $result = JobResult::PASSED;
    private string $output = "";
    /** @var int Total elapsed time in milliseconds */
    private int $totalTime = 0;
    /**
     * @internal
     */
    public int $totalAssertions = 0;
    /** @var callable[] */
    public array $onAfterExecute = [];

    public function __construct(
        string $name,
        callable $callback,
        array $params = [],
        bool|string $skip = false,
        array $onAfterExecute = []
    ) {
        $this->name = $name;
        $this->callback = $callback;
        $this->params = $params;
        $this->skip = $skip;
        $this->onAfterExecute = $onAfterExecute;
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
