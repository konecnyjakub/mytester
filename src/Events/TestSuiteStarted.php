<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * Triggers when execution of a test suite started
 *
 * @author Jakub Konečný
 */
final readonly class TestSuiteStarted
{
    public function __construct(public TestCase $testSuite)
    {
    }
}
