<?php
declare(strict_types=1);

namespace MyTester\Events;

/**
 * Triggers when a deprecation is triggered in a test
 *
 * @author Jakub Konečný
 */
final readonly class DeprecationTriggered
{
    public function __construct(public string $message, public string $fileName, public int $fileLine)
    {
    }
}
