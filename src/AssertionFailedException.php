<?php
declare(strict_types=1);

namespace MyTester;

use Throwable;

/**
 * Assertion failure in test
 *
 * @internal
 */
class AssertionFailedException extends InterruptedTestException
{
    public readonly string $assertionMessage;
    public readonly int $assertionNumber;

    public function __construct(string $assertionMessage = "", int $assertionNumber = 0, ?Throwable $previous = null)
    {
        $this->assertionMessage = $assertionMessage;
        $this->assertionNumber = $assertionNumber;
        $message = sprintf("Test %d failed. %s", $assertionNumber, $assertionMessage);
        parent::__construct($message, 0, $previous);
    }
}
