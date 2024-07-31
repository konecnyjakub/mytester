<?php

declare(strict_types=1);

namespace MyTester;

trait TAssertions
{
    /** @internal */
    protected int $taskCount = 0;

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

    /**
     * Tries an assertion
     */
    protected function assert(mixed $code, string $failureText = ""): void
    {
        $success = ($code == true);
        $message = "";
        if (!$code) {
            $message = ($failureText === "") ? "The assertion is not true." : $failureText;
        }
        $this->testResult($message, $success);
    }

    /**
     * Are both values same?
     */
    protected function assertSame(mixed $expected, mixed $actual): void
    {
        $success = ($expected == $actual);
        $message = ($success) ? "" : "The value is not $expected but $actual.";
        $this->testResult($message, $success);
    }

    /**
     * Are not both values same?
     */
    protected function assertNotSame(mixed $expected, mixed $actual): void
    {
        $success = ($expected !== $actual);
        $message = ($success) ? "" : "The value is $expected.";
        $this->testResult($message, $success);
    }

    /**
     * Is $actual greater than $expected?
     */
    protected function assertGreaterThan(int|float $expected, int|float $actual): void
    {
        $success = ($actual > $expected);
        $message = ($success) ? "" : "$actual is not greater than $expected.";
        $this->testResult($message, $success);
    }

    /**
     * Is $actual less than $expected?
     */
    protected function assertLessThan(int|float $expected, int|float $actual): void
    {
        $success = ($actual < $expected);
        $message = ($success) ? "" : "$actual is not less than $expected.";
        $this->testResult($message, $success);
    }

    /**
     * Is $actual equal to true?
     */
    protected function assertTrue(bool $actual): void
    {
        $success = ($actual);
        $message = ($success) ? "" : "The value is not true.";
        $this->testResult($message, $success);
    }

    /**
     * Is the expression true?
     */
    protected function assertTruthy(mixed $actual): void
    {
        $success = ($actual == true);
        $message = ($success) ? "" : "The expression is not true.";
        $this->testResult($message, $success);
    }

    /**
     * Is $actual equal to false?
     */
    protected function assertFalse(bool $actual): void
    {
        $success = (!$actual);
        $message = ($success) ? "" : "The value is not false.";
        $this->testResult($message, $success);
    }

    /**
     * Is the expression false?
     */
    protected function assertFalsey(mixed $actual): void
    {
        $success = ($actual == false);
        $message = ($success) ? "" : "The expression is not false.";
        $this->testResult($message, $success);
    }

    /**
     * Is the value null?
     */
    protected function assertNull(mixed $actual): void
    {
        $success = ($actual == null);
        $message = ($success) ? "" : "The value is not null.";
        $this->testResult($message, $success);
    }

    /**
     * Is not the value null?
     */
    protected function assertNotNull(mixed $actual): void
    {
        $success = ($actual !== null);
        $message = ($success) ? "" : "The value is null.";
        $this->testResult($message, $success);
    }

    /**
     * Does $actual contain $needle?
     */
    protected function assertContains(string|array $needle, string|array $actual): void
    {
        if (is_string($actual) && is_string($needle)) {
            $success = ($needle !== "" && str_contains($actual, $needle));
            $message = ($success) ? "" : "$needle is not in the variable.";
            $this->testResult($message, $success);
        } elseif (is_array($actual)) {
            $success = (in_array($needle, $actual));
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
            $success = ($needle === "" || !str_contains($actual, $needle));
            $message = ($success) ? "" : "$needle is in the variable.";
            $this->testResult($message, $success);
        } elseif (is_array($actual)) {
            $success = (!in_array($needle, $actual));
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
            trigger_error("Passing string as parameter \$value to " . __METHOD__ . " is deprecated", E_USER_DEPRECATED);
            $this->testResult("The variable is not array or countable object.", false);
            return;
        }
        $actual = count($value);
        $success = ($actual === $count);
        $message = ($success) ? "" : "Count of the variable is $actual.";
        $this->testResult($message, $success);
    }

    /**
     * Does $value not contain $count items?
     */
    protected function assertNotCount(int $count, string|array|\Countable $value): void
    {
        if (!is_array($value) && !$value instanceof \Countable) {
            trigger_error("Passing string as parameter \$value to " . __METHOD__ . " is deprecated", E_USER_DEPRECATED);
            $this->testResult("The variable is not array or countable object.", false);
            return;
        }
        $actual = count($value);
        $success = ($actual !== $count);
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
            $success = (call_user_func("is_$type", $value));
            $actual = gettype($value);
            $message = ($success) ? "" : "The variable is $actual.";
            $this->testResult($message, $success);
            return;
        }
        $success = ($value instanceof $type);
        $actual = get_debug_type($value);
        $message = ($success) ? "" : "The variable is instance of $actual.";
        $this->testResult($message, $success);
    }

    /**
     * Does the code throw the expected exception?
     *
     * @param class-string $className
     */
    protected function assertThrowsException(callable $callback, string $className, string $message = null, int $code = null): void
    {
        $success = false;
        $errorMessage = "";
        $e = null;
        try {
            $callback();
        } catch (\Throwable $e) {
            if ($e instanceof $className) {
                if (($message === null || $e->getMessage() === $message) && ($code === null || $e->getCode() === $code)) {
                    $success = true;
                }
            }
        }
        if (!$success) {
            if ($e === null) {
                $errorMessage = "The code does not throw any exception.";
            } elseif (!$e instanceof $className) {
                $errorMessage = "The code does not throw $className but " . get_class($e) . ".";
            } elseif ($message !== null && $message !== $e->getMessage()) {
                $errorMessage = "The code does not throw an exception with message '$message' but '{$e->getMessage()}'.";
            } elseif ($code !== null && $code !== $e->getCode()) {
                $errorMessage = "The code does not throw an exception with code $code but {$e->getCode()}.";
            }
        }
        $this->testResult($errorMessage, $success);
    }

    /**
     * Is output of code $expected?
     */
    protected function assertOutput(callable $callback, string $expected): void
    {
        ob_start();
        $callback();
        /** @var string $output */
        $output = ob_get_clean();
        $success = ($expected == $output);
        $message = ($success) ? "" : "Output of code  is not '$expected' but '$output'.";
        $this->testResult($message, $success);
    }
}
