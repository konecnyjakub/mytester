<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\Job;

/**
 * Triggers when a test failed
 *
 * @author Jakub Konečný
 */
final readonly class TestFailed
{
    public function __construct(public Job $test)
    {
    }
}
