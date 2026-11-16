<?php
declare(strict_types=1);

namespace MyTester;

interface RetryStrategy
{
    public function shouldRetry(int $currentAttemptNumber, int $maxAttemptsNumber, JobResult $currentAttemptResult): bool;
}
