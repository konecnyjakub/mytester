<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * @author Jakub Konečný
 */
final readonly class TestCaseFinished
{
    public function __construct(public TestCase $testCase)
    {
    }
}
