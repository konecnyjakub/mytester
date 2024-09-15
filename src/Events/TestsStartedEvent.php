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
    /**
     * @param TestCase[] $testCases
     */
    public function __construct(public array $testCases = [])
    {
    }
}
