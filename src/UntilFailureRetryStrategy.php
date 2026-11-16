<?php
declare(strict_types=1);

namespace MyTester;

final readonly class UntilFailureRetryStrategy implements RetryStrategy
{
    public function shouldRetry(int $currentAttemptNumber, int $maxAttemptsNumber, JobResult $currentAttemptResult): bool
    {
        if ($currentAttemptNumber >= $maxAttemptsNumber) {
            return false;
        } elseif (in_array($currentAttemptResult, [JobResult::PASSED, JobResult::WARNING,], true)) {
            return true;
        }
        return false;
    }
}
