<?php

declare(strict_types=1);

namespace MyTester;

/**
 * Skipped test info
 *
 * @author Jakub Konečný
 */
final class SkippedTest
{
    use \Nette\SmartObject;

    public string $name;
    public string $reason;

    public function __construct(string $name, string $reason)
    {
        $this->name = $name;
        $this->reason = $reason;
    }
}
