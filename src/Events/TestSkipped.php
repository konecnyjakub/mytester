<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\Job;

/**
 * Triggers when a test is skipped
 *
 * @author Jakub Konečný
 */
final readonly class TestSkipped
{
    public function __construct(public Job $test)
    {
    }
}
