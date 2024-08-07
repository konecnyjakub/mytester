<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\IResultsFormatter;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Helper
{
    use \Nette\StaticClass;

    public static array $availableFormatters = [
        "junit" => JUnit::class,
        "tap" => Tap::class,
        "testdox" => TestDox::class,
    ];

    /**
     * @param string $filename {@see IResultsFormatter::getOutputFileName()}
     */
    public static function isFileOutput(string $filename): bool
    {
        $consoleOutputs = [
            "php://stdout", "php://stderr", "php://output",
        ];
        return !in_array($filename, $consoleOutputs, true);
    }
}
