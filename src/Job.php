<?php
declare(strict_types=1);

namespace MyTester;

/**
 * One job of the test suite
 *
 * @author Jakub Konečný
 * @property-read callable $callback
 * @property-read bool|string $skip
 * @property-read JobResult $result
 * @property-read string $output @internal
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

    /**
     * Executes the task
     */
    public function execute(): void
    {
        if (!$this->skip) {
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
        }
        $this->result = JobResult::fromJob($this);
    }
}
