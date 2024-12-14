<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Helper for colorizing output in console
 *
 * @author Jakub Konečný
 */
final class ConsoleColors
{
    private const array COLORS = [
        "black" => "0;30",
        "maroon" => "0;31",
        "green" => "0;32",
        "olive" => "0;33",
        "navy" => "0;34",
        "magenta" => "0;35",
        "teal" => "0;36",
        "silver" => "0;37",
        "gray" => "1;30",
        "red" => "1;31",
        "lime" => "1;32",
        "yellow" => "1;33",
        "blue" => "1;34",
        "fuchsia" => "1;35",
        "aqua" => "1;36",
        "white" => "1;37",
    ];

    public bool $useColors = false;

    public function color(string $text, ?string $color = null): string
    {
        if (!$this->useColors || $color === null || !array_key_exists($color, static::COLORS)) {
            return $text;
        }
        return "\033[" . static::COLORS[$color] . "m" . $text . "\033[0m";
    }
}
