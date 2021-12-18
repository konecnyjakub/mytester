<?php

declare(strict_types=1);

namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @property-read callable $callback
 * @property-read string $result
 * @property-read string $output @internal
 * @method void onAfterExecute()
 */
final class Job
{
    use \Nette\SmartObject;

    public readonly string $name;
    /** @var callable Task */
    protected $callback;
    public readonly array $params;
    // phpcs:ignore
    public readonly bool|string $skip;
    protected JobResult $result = JobResult::PASSED;
    protected string $output = "";
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

    protected function getResult(): JobResult
    {
        return $this->result;
    }

    protected function getOutput(): string
    {
        return $this->output;
    }

    /**
     * Executes the task
     */
    public function execute(): void
    {
        if (!$this->skip) {
            ob_start();
            call_user_func_array($this->callback, $this->params);
            $this->onAfterExecute();
            /** @var string $output */
            $output = ob_get_clean();
            $this->output = $output;
        }
        $this->result = JobResult::fromJob($this);
    }
}
