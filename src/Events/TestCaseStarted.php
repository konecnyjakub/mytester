<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * @author Jakub Konečný
 * @internal
 */
final readonly class TestCaseStarted
{
    public function __construct(public TestCase $testCase)
    {
    }
}
