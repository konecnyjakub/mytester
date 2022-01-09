<?php

declare(strict_types=1);

namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @property-read string $name
 * @property-read callable $callback
 * @property-read array $params
 * @property-read bool|string $skip
 * @property-read string $result
 * @property-read string $output @internal
 * @method void onAfterExecute()
 */
final class Job
{
    use \Nette\SmartObject;

    public const RESULT_PASSED = "passed";
    public const RESULT_SKIPPED = "skipped";
    public const RESULT_FAILED = "failed";
    public const RESULT_PASSED_WITH_WARNINGS = "warning";

    protected string $name;
    /** @var callable Task */
    protected $callback;
    protected array $params = [];
    protected bool|string $skip;
    protected string $result = self::RESULT_PASSED;
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

    protected function getName(): string
    {
        return $this->name;
    }

    protected function getCallback(): callable
    {
        return $this->callback;
    }

    protected function getParams(): array
    {
        return $this->params;
    }

    protected function getSkip(): bool|string
    {
        return $this->skip;
    }

    protected function getResult(): string
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
        if ($this->skip) {
            $this->result = static::RESULT_SKIPPED;
        } else {
            ob_start();
            call_user_func_array($this->callback, $this->params);
            $this->onAfterExecute();
            /** @var string $output */
            $output = ob_get_clean();
            if (str_contains($output, " failed. ")) {
                $this->result = static::RESULT_FAILED;
            } elseif (str_starts_with($output, "Warning: ")) {
                $this->result = static::RESULT_PASSED_WITH_WARNINGS;
            }
            $this->output = $output;
        }
    }
}
