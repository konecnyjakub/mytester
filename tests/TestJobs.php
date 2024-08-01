<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Tests for class Job
 *
 * @author Jakub Konečný
 */
final class TestJobs
{
    private JobTest $testCase;

    public function __construct(JobTest $testCase)
    {
        $this->testCase = $testCase;
    }

    public function test(): void
    {
        $this->testCase->assertSame(1, 1);
    }

    /**
     * Test params for job
     */
    public function testParams(array $params, string $text): void
    {
        $this->testCase->assertSame("abc", $params[0]);
        $this->testCase->assertSame("def", $text);
    }
}
