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
    use \Nette\SmartObject;

    public string $name;
    public string $reason;

    public function __construct(string $name, string $reason)
    {
        $this->name = $name;
        $this->reason = $reason;
    }

    public function __toString(): string
    {
        $reason = "";
        if ($this->reason) {
            $reason = ": $this->reason";
        }
        return "Skipped $this->name$reason\n";
    }
}
