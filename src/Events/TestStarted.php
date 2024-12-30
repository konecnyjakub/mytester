<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\Job;

/**
 * Triggers when execution of a test started
 *
 * @author Jakub Konečný
 */
final readonly class TestStarted
{
    public function __construct(public Job $test)
    {
    }
}
