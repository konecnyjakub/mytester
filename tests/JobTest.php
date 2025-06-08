<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventDispatcher;
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
        $rp = new \ReflectionProperty(TestCase::class, "eventDispatcher");
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $rp->getValue($this);
        $job = new Job("Test Job", [$test, "test"]);
        $job->setEventDispatcher($eventDispatcher);
        $params = [
            ["abc"], "def"
        ];
        $job2 = new Job("Test Job with Params", [$test, "testParams"], $params);
        $job2->setEventDispatcher($eventDispatcher);
        $job3 = new Job("Test Skipped Job", [$test, "test"], [], true);
        $job3->setEventDispatcher($eventDispatcher);
        return [$job, $job2, $job3];
    }
}
