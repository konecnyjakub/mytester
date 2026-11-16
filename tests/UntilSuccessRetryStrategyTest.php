<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class UntilSuccessRetryStrategy
 *
 * @author Jakub Konečný
 */
#[TestSuite("UntilSuccessRetryStrategy")]
#[Group("retryStrategies")]
final class UntilSuccessRetryStrategyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $strategy = new UntilSuccessRetryStrategy();
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::PASSED));
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::WARNING));
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::FAILED));
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::SKIPPED));
        $this->assertFalse($strategy->shouldRetry(1, 2, JobResult::PASSED));
        $this->assertFalse($strategy->shouldRetry(1, 2, JobResult::WARNING));
        $this->assertTrue($strategy->shouldRetry(1, 2, JobResult::FAILED));
        $this->assertFalse($strategy->shouldRetry(1, 2, JobResult::SKIPPED));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::PASSED));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::WARNING));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::FAILED));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::SKIPPED));
    }
}
