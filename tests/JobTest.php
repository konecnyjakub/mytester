<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class Job
 *
 * @author Jakub Konečný
 */
#[TestSuite("JobTest")]
final class JobTest extends TestCase
{
    public function assertSame(mixed $expected, mixed $actual): void
    {
        parent::assertSame($expected, $actual);
    }

    protected function getJobs(): array
    {
        $test = new TestJobs($this);
        $job = new Job("Test Job", [$test, "test"]);
        $params = [
            ["abc"], "def"
        ];
        $job2 = new Job("Test Job with Params", [$test, "testParams"], $params);
        $job3 = new Job("Test Skipped Job", [$test, "test"], [], true);
        return [$job, $job2, $job3];
    }
}
