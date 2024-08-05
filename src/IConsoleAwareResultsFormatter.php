<?php
declare(strict_types=1);

namespace MyTester;

use Nette\CommandLine\Console;

/**
 * Results formatter for {@see Tester}
 *
 * @author Jakub Konečný
 * @internal
 * @deprecated
 */
interface IConsoleAwareResultsFormatter extends IResultsFormatter
{
    public function setConsole(Console $console): void;
}
