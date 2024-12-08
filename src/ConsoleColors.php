<?php
declare(strict_types=1);

namespace MyTester;

use Nette\CommandLine\Console;

/**
 * Helper for colorizing output in console
 *
 * @author Jakub Konečný
 * @property bool $useColors
 */
final class ConsoleColors
{
    use \Nette\SmartObject;

    private readonly Console $console;

    private bool $useColors = false;

    public function __construct()
    {
        $this->console = new Console();
        $this->setUseColors(false);
    }

    public function color(string $text, ?string $color = null): string
    {
        return $this->console->color($color, $text);
    }

    protected function isUseColors(): bool
    {
        return $this->useColors;
    }

    protected function setUseColors(bool $useColors): void
    {
        $this->useColors = $useColors;
        $this->console->useColors($useColors);
    }
}
