<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Results formatter for {@see Tester}
 *
 * @author Jakub Konečný
 * @internal
 */
interface IConsoleAwareResultsFormatter extends IResultsFormatter
{
    public function setConsole(ConsoleColors $console): void;
}
