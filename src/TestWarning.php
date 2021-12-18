<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test warning info
 *
 * @author Jakub Konečný
 */
class TestWarning
{
    use \Nette\SmartObject;

    public readonly string $name;
    public readonly string $text;

    public function __construct(string $name, string $text)
    {
        $this->name = $name;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return "$this->name passed with warning: $this->text";
    }
}
