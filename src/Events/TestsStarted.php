<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * Triggers when execution of tests started
 *
 * @author Jakub Konečný
 */
final readonly class TestsStarted
{
    /**
     * @param TestCase[] $testCases
     */
    public function __construct(public array $testCases = [])
    {
    }
}
