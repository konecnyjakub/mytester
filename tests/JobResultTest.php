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

        $job = new Job("Test Job", static function () {
        });
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::PASSED, JobResult::fromJob($job));

        $job = new Job("Test Job", static function () {
            echo "Test failed. Reason";
        });
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::FAILED, JobResult::fromJob($job));

        $job = new Job("Test Job", static function () {
            throw new \Exception();
        });
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::FAILED, JobResult::fromJob($job));

        $job = new Job("Test Job", static function () {
            echo "Warning: Text";
        });
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::WARNING, JobResult::fromJob($job));

        $job = new Job("Test Job", static function () {
            trigger_error("test", E_USER_DEPRECATED);
        });
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::WARNING, JobResult::fromJob($job));
        $this->assertContains("deprecated \"test\"", $job->output);

        $job = new Job("Test Job", static function () {
            trigger_error("test", E_USER_DEPRECATED);
        }, reportDeprecations: false);
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::PASSED, JobResult::fromJob($job));
        $this->assertNotContains("deprecated \"test\"", $job->output);

        $job = new Job("Test Job", static function () {
        }, [], true);
        $job->setEventDispatcher($eventDispatcher);
        $job->execute();
        $this->assertSame(JobResult::SKIPPED, JobResult::fromJob($job));
    }
}
