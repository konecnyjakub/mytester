<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * @author Jakub Konečný
 * @internal
 */
final class TestCaseStarted
{
    public TestCase $testCase;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }
}
