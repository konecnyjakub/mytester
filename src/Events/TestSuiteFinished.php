<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * Triggers when execution of a test suite finished
 *
 * @author Jakub Konečný
 */
final readonly class TestSuiteFinished
{
    public function __construct(public TestCase $testSuite)
    {
    }
}
