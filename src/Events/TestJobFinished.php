<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\Job;

/**
 * Triggers when execution of a test job finished
 *
 * @author Jakub Konečný
 */
final readonly class TestJobFinished
{
    public function __construct(public Job $job)
    {
    }
}
