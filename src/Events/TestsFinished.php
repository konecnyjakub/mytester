<?php
declare(strict_types=1);

namespace MyTester\Events;

use MyTester\TestCase;

/**
 * Triggers when execution of tests finished
 *
 * @author Jakub Konečný
 */
final readonly class TestsFinished
{
    /**
     * @param TestCase[] $testSuites
     */
    public function __construct(public array $testSuites = [])
    {
    }
}
