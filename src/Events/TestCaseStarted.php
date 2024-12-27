<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * Triggers when execution of a test case started
 *
 * @author Jakub Konečný
 */
final readonly class TestCaseStarted
{
    public function __construct(public TestCase $testCase)
    {
    }
}
