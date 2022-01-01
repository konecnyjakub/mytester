<?php

declare(strict_types=1);

namespace MyTester;

trait TAssertions
{
    /** @internal */
    protected int $taskCount = 0;
    /** @internal */
    protected bool $shouldFail = false;

    /**
     * Prints result of a test
     *
     * @internal
     */
    protected function testResult(string $text, bool $success = true): void
    {
        $this->incCounter();
        if ($success) {
            return;
        }
        echo "Test $this->taskCount failed. $text\n";
    }

    /**
     * Increases task counter
     *
     * @internal
     */
    protected function incCounter(): void
    {
        $this->taskCount++;
    }

    /**
     * Resets task counter
     *
     * @internal
     */
    protected function resetCounter(): void
    {
        $this->taskCount = 0;
    }

    /**
     * @internal
     */
    protected function getCounter(): int
    {
        return $this->taskCount;
    }

    protected function showStringOrArray(string|array $variable): string
    {
        return (is_string($variable) ? $variable : "(array)");
    }

    protected function isSuccess(bool $success): bool
    {
        if ($this->shouldFail) {
            $success = !$success;
        }
        return $success;
    }

    /**
     * Tries an assertion
     */
    protected function assert(mixed $code, string $failureText = ""): void
    {
        $success = $this->isSuccess($code == true);
        $message = "";
        if (!$success) {
            $message = ($failureText === "") ? "Assertion \"$code\" is not true." : $failureText;
        }
        $this->testResult($message, $success);
    }

    /**
     * Are both values same?
     */
    protected function assertSame(mixed $expected, mixed $actual): void
    {
        $success = $this->isSuccess($expected == $actual);
        $message = ($success) ? "" : "The value is not $expected but $actual.";
        $this->testResult($message, $success);
    }

    /**
     * Are not both values same?
     */
    protected function assertNotSame(mixed $expected, mixed $actual): void
    {
        $success = $this->isSuccess($expected !== $actual);
        $message = ($success) ? "" : "The value is $expected.";
        $this->testResult($message, $success);
    }

    /**
     * Is $actual equal to true?
     */
    protected function assertTrue(bool $actual): void
    {
        $success = $this->isSuccess($actual);
        $message = ($success) ? "" : "The value is not true.";
        $this->testResult($message, $success);
    }

    /**
     * Is the expression true?
     */
    protected function assertTruthy(mixed $actual): void
    {
        $success = $this->isSuccess($actual == true);
        $message = ($success) ? "" : "The expression is not true.";
        $this->testResult($message, $success);
    }

    /**
     * Is $actual equal to false?
     */
    protected function assertFalse(bool $actual): void
    {
        $success = $this->isSuccess(!$actual);
        $message = ($success) ? "" : "The value is not false.";
        $this->testResult($message, $success);
    }

    /**
     * Is the expression false?
     */
    protected function assertFalsey(mixed $actual): void
    {
        $success = $this->isSuccess($actual == false);
        $message = ($success) ? "" : "The expression is not false.";
        $this->testResult($message, $success);
    }

    /**
     * Is the value null?
     */
    protected function assertNull(mixed $actual): void
    {
        $success = $this->isSuccess($actual == null);
        $message = ($success) ? "" : "The value is not null.";
        $this->testResult($message, $success);
    }

    /**
     * Is not the value null?
     */
    protected function assertNotNull(mixed $actual): void
    {
        $success = $this->isSuccess($actual !== null);
        $message = ($success) ? "" : "The value is null.";
        $this->testResult($message, $success);
    }

    /**
     * Does $actual contain $needle?
     */
    protected function assertContains(string|array $needle, string|array $actual): void
    {
        if (is_string($actual) && is_string($needle)) {
            $success = $this->isSuccess($needle !== "" && str_contains($actual, $needle));
            if ($success) {
                $this->testResult("");
            } else {
                $this->testResult("$needle is not in the variable.", false);
            }
        } elseif (is_array($actual)) {
            $success = $this->isSuccess(in_array($needle, $actual));
            $message = ($success) ? "" : $this->showStringOrArray($needle) . " is not in the variable.";
            $this->testResult($message, $success);
        } else {
            $this->testResult($this->showStringOrArray($needle) . " is not in the variable.", false);
        }
    }

    /**
     * Does $actual not contain $needle?
     */
    protected function assertNotContains(string|array $needle, string|array $actual): void
    {
        if (is_string($actual) && is_string($needle)) {
            $success = $this->isSuccess($needle === "" || !str_contains($actual, $needle));
            $message = ($success) ? "" : "$needle is in the variable.";
            $this->testResult($message, $success);
        } elseif (is_array($actual)) {
            $success = $this->isSuccess(!in_array($needle, $actual));
            $message = ($success) ? "" : $this->showStringOrArray($needle) . " is in the variable.";
            $this->testResult($message, $success);
        } else {
            $this->testResult($this->showStringOrArray($needle) . " is not in the variable.", false);
        }
    }

    /**
     * Does $value contain $count items?
     */
    protected function assertCount(int $count, string|array|\Countable $value): void
    {
        if (!is_array($value) && !$value instanceof \Countable) {
            $this->testResult("The variable is not array or countable object.", false);
            return;
        }
        $actual = count($value);
        $success = $this->isSuccess($actual === $count);
        $message = ($success) ? "" : "Count of the variable is $actual.";
        $this->testResult($message, $success);
    }

    /**
     * Does $value not contain $count items?
     */
    protected function assertNotCount(int $count, string|array|\Countable $value): void
    {
        if (!is_array($value) && !$value instanceof \Countable) {
            $this->testResult("The variable is not array or countable object.", false);
            return;
        }
        $actual = count($value);
        $success = $this->isSuccess($actual !== $count);
        $message = ($success) ? "" : "Count of the variable is $actual.";
        $this->testResult($message, $success);
    }

    /**
     * Is $value of type $type?
     */
    protected function assertType(string|object $type, mixed $value): void
    {
        if (
            in_array($type, [
            "array", "bool", "float", "int", "string", "null", "object", "resource",
            "scalar", "iterable", "callable",
            ], true)
        ) {
            $success = $this->isSuccess(call_user_func("is_$type", $value));
            $actual = gettype($value);
            $message = ($success) ? "" : "The variable is $actual.";
            $this->testResult($message, $success);
            return;
        }
        $success = $this->isSuccess($value instanceof $type);
        $actual = get_debug_type($value);
        $message = ($success) ? "" : "The variable is instance of $actual.";
        $this->testResult($message, $success);
    }
}
