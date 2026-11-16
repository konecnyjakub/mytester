<?php
declare(strict_types=1);

namespace MyTester;

final readonly class UntilSuccessRetryStrategy implements RetryStrategy
{
    public function shouldRetry(int $currentAttemptNumber, int $maxAttemptsNumber, JobResult $currentAttemptResult): bool
    {
        if ($currentAttemptNumber >= $maxAttemptsNumber) {
            return false;
        } elseif ($currentAttemptResult === JobResult::FAILED) {
            return true;
        }
        return false;
    }
}
