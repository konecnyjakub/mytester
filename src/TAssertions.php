<?php
declare(strict_types=1);

namespace MyTester;

trait TAssertions
{
    private int $taskCount = 0;

    /**
     * Prints result of a test
     */
    final protected function testResult(string $text, bool $success = true): void
    {
        $this->incCounter();
        if ($success) {
            return;
        }
        throw new AssertionFailedException($text, $this->getCounter());
    }

    /**
     * Increases task counter
     */
    final protected function incCounter(): void
    {
        $this->taskCount++;
    }

    /**
     * Resets task counter
     */
    final protected function resetCounter(): void
    {
        $this->taskCount = 0;
    }

    final protected function getCounter(): int
    {
        return $this->taskCount;
    }

    protected function showValue(mixed $variable): string
    {
        if (is_string($variable)) {
            return "'$variable'";
        }
        return var_export($variable, true);
    }

    /**
     * Tries an assertion
     */
    protected function assert(mixed $code, string $failureText = ""): void
    {
        $success = ((bool) $code === true);
        $message = "";
        if (!$success) {
            $message = ($failureText === "") ? "The assertion is not true." : $failureText;
        }
        $this->testResult($message, $success);
    }

    /**
     * Are both values same?
     */
    protected function assertSame(mixed $expected, mixed $actual): void
    {
        $success = ($expected == $actual); // @phpstan-ignore equal.notAllowed
        $message = "";
        if (!$success) {
            $message = sprintf("The value is not %s but %s.", $this->showValue($expected), $this->showValue($actual));
        }
        $this->testResult($message, $success);
    }

    /**
     * Are not both values same?
     */
    protected function assertNotSame(mixed $expected, mixed $actual): void
    {
        $success = ($expected !== $actual);
        $message = "";
        if (!$success) {
            $message = sprintf("The value is %s.", $this->showValue($expected));
        }
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
        $success = ((bool) $actual === true);
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
        $success = ((bool) $actual === false);
        $message = ($success) ? "" : "The expression is not false.";
        $this->testResult($message, $success);
    }

    /**
     * Is the value null?
     */
    protected function assertNull(mixed $actual): void
    {
        $success = ($actual === null);
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
            $success = (in_array($needle, $actual, true));
            $message = ($success) ? "" : $this->showValue($needle) . " is not in the variable.";
            $this->testResult($message, $success);
        } else {
            $this->testResult($this->showValue($needle) . " is not in the variable.", false);
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
            $success = (!in_array($needle, $actual, true));
            $message = ($success) ? "" : $this->showValue($needle) . " is in the variable.";
            $this->testResult($message, $success);
        } else {
            $this->testResult($this->showValue($needle) . " is not in the variable.", false);
        }
    }

    /**
     * Does $value contain $count items?
     */
    protected function assertCount(int $count, array|\Countable $value): void
    {
        $actual = count($value);
        $success = ($actual === $count);
        $message = ($success) ? "" : "Count of the variable is not $count but $actual.";
        $this->testResult($message, $success);
    }

    /**
     * Does $value not contain $count items?
     */
    protected function assertNotCount(int $count, array|\Countable $value): void
    {
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
    protected function assertThrowsException(
        callable $callback,
        string $className,
        ?string $message = null,
        ?int $code = null
    ): void {
        $success = false;
        $errorMessage = "";
        $e = null;
        try {
            $callback();
        } catch (\Throwable $e) {
            if ($e instanceof $className) {
                if (
                    ($message === null || $e->getMessage() === $message) && ($code === null || $e->getCode() === $code)
                ) {
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
                $errorMessage =
                    "The code does not throw an exception with message '$message' but '{$e->getMessage()}'.";
            } elseif ($code !== null && $code !== $e->getCode()) {
                $errorMessage = "The code does not throw an exception with code $code but {$e->getCode()}.";
            }
        }
        $this->testResult($errorMessage, $success);
    }

    protected function assertNoException(callable $callback): void
    {
        $e = null;
        try {
            $callback();
        } catch (\Throwable $e) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
        }
        $success = ($e === null);
        $message = ($success) ? "" : "No exception was expected but " . $e::class . " was thrown.";
        $this->testResult($message, $success);
    }

    /**
     * Is output of code $expected?
     */
    protected function assertOutput(callable $callback, string $expected): void
    {
        ob_start();
        $callback();
        $output = (string) ob_get_clean();
        $success = ($expected === $output);
        $message = ($success) ? "" : "Output of code  is not '$expected' but '$output'.";
        $this->testResult($message, $success);
    }

    /**
     * Does $actual matches regular expression $expected?
     */
    protected function assertMatchesRegExp(string $expected, string $actual): void
    {
        $success = (preg_match($expected, $actual) === 1);
        $message = ($success) ? "" : "The string does not match regular expression.";
        $this->testResult($message, $success);
    }

    /**
     * Does $actual matches content of file $filename?
     */
    protected function assertMatchesFile(string $filename, string $actual): void
    {
        $expected = @file_get_contents($filename);
        if ($expected === false) {
            $this->testResult("File $filename could not be loaded.", false);
        }
        $this->assertSame($expected, $actual);
    }

    /**
     * Is $actual an array consisting only of instances of $className
     *
     * @param class-string $className
     */
    protected function assertArrayOfClass(string $className, array $actual): void
    {
        if (count($actual) === 0) {
            $this->testResult("The array is empty.", false);
        }
        $success = array_all($actual, function (mixed $value) use ($className) {
            return $value instanceof $className;
        });
        $message = ($success) ? "" : "The array does not contain only instances of $className.";
        $this->testResult($message, $success);
    }

    protected function assertTriggersDeprecation(callable $callback, string $expected = ""): void
    {
        $deprecation = "";
        set_error_handler(
            function (int $errno, string $errstr, string $errfile, int $errline) use (&$deprecation) {
                $deprecation = $errstr;
                return true;
            },
            E_USER_DEPRECATED
        );
        $callback();
        restore_error_handler();
        if ($deprecation === "") {
            $success = false;
            $message = "Expected a deprecation but none was triggered.";
        } else {
            $success = ($expected === "" || $deprecation === $expected);
            $message = ($success) ? "" : "Expected deprecation '$expected' but '$deprecation' was triggered.";
        }
        $this->testResult($message, $success);
    }
}
