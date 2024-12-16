<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Results formatter for {@see Tester} that can print results into console
 * Injects a helper for colorizing output
 *
 * @author Jakub Konečný
 */
interface IConsoleAwareResultsFormatter extends IResultsFormatter
{
    public function setConsole(ConsoleColors $console): void;
}
