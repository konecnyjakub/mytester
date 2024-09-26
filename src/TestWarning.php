<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test warning info
 *
 * @author Jakub Konečný
 */
final readonly class TestWarning
{
    public function __construct(public string $name, public string $text)
    {
    }

    public function __toString(): string
    {
        return "$this->name passed with warning: $this->text";
    }
}
