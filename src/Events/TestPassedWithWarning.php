<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\Job;

/**
 * Triggers when a test passed with warning
 *
 * @author Jakub Konečný
 */
final readonly class TestPassedWithWarning
{
    public function __construct(public Job $test)
    {
    }
}
