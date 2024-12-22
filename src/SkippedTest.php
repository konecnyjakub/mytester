<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Skipped test info
 *
 * @author Jakub Konečný
 */
final readonly class SkippedTest
{
    public function __construct(public string $name, public string $reason)
    {
    }

    public function __toString(): string
    {
        $reason = "";
        if ($this->reason !== "") {
            $reason = ": $this->reason";
        }
        return "Skipped $this->name$reason\n";
    }
}
