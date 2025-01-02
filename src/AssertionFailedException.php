<?php
declare(strict_types=1);

namespace MyTester;

use Throwable;

/**
 * Assertion failure in test
 *
 * @author Jakub Konečný
 */
class AssertionFailedException extends InterruptedTestException
{
    public function __construct(
        public readonly string $assertionMessage = "",
        public readonly int $assertionNumber = 0,
        ?Throwable $previous = null
    ) {
        $message = sprintf("Test %d failed. %s", $assertionNumber, $assertionMessage);
        parent::__construct($message, 0, $previous);
    }
}
