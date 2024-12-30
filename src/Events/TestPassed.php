<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\Job;

/**
 * Triggers when a test passed
 *
 * @author Jakub Konečný
 */
final readonly class TestPassed
{
    public function __construct(public Job $test)
    {
    }
}
