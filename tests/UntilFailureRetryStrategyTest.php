<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class UntilFailureRetryStrategy
 *
 * @author Jakub Konečný
 */
#[TestSuite("UntilFailureRetryStrategy")]
#[Group("retryStrategies")]
final class UntilFailureRetryStrategyTest extends TestCase
{
    public function testShouldRetry(): void
    {
        $strategy = new UntilFailureRetryStrategy();
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::PASSED));
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::WARNING));
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::FAILED));
        $this->assertFalse($strategy->shouldRetry(1, 0, JobResult::SKIPPED));
        $this->assertTrue($strategy->shouldRetry(1, 2, JobResult::PASSED));
        $this->assertTrue($strategy->shouldRetry(1, 2, JobResult::WARNING));
        $this->assertFalse($strategy->shouldRetry(1, 2, JobResult::FAILED));
        $this->assertFalse($strategy->shouldRetry(1, 2, JobResult::SKIPPED));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::PASSED));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::WARNING));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::FAILED));
        $this->assertFalse($strategy->shouldRetry(2, 2, JobResult::SKIPPED));
    }
}
