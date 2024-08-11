<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * @author Jakub Konečný
 * @internal
 */
final class TestsStartedEvent
{
    /** @var TestCase[] */
    public array $testCases = [];

    public function __construct(array $testCases)
    {
        $this->testCases = $testCases;
    }
}
