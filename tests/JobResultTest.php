<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventDispatcher;
use MyTester\Attributes\TestSuite;

/**
 * Test suite for class JobResult
 *
 * @author Jakub Konečný
 */
#[TestSuite("JobResultTest")]
final class JobResultTest extends TestCase
{
    public function testFromJob(): void
    {
        $rp = new \ReflectionProperty(TestCase::class, "eventDispatcher");
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $rp->getValue($this);

        $job = new Job("Test Job", function () {
        });
        $job->execute();
        $this->assertSame(JobResult::PASSED, JobResult::fromJob($job));

        $job = new Job("Test Job", function () {
            echo "Test failed. Reason";
        });
        $job->execute();
        $this->assertSame(JobResult::FAILED, JobResult::fromJob($job));

        $job = new Job("Test Job", function () {
            throw new \Exception();
        });
        $job->execute();
        $this->assertSame(JobResult::FAILED, JobResult::fromJob($job));

        $job = new Job("Test Job", function () {
            echo "Warning: Text";
        });
        $job->execute();
        $this->assertSame(JobResult::WARNING, JobResult::fromJob($job));

        $job = new Job("Test Job", function () {
            trigger_error("test", E_USER_DEPRECATED);
        });
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::WARNING, JobResult::fromJob($job));
        $this->assertContains("deprecated \"test\"", $job->output);

        $job = new Job("Test Job", function () {
            trigger_error("test", E_USER_DEPRECATED);
        }, reportDeprecations: false);
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::PASSED, JobResult::fromJob($job));
        $this->assertNotContains("deprecated \"test\"", $job->output);

        $job = new Job("Test Job", function () {
        }, [], true);
        $job->execute();
        $this->assertSame(JobResult::SKIPPED, JobResult::fromJob($job));
    }
}
