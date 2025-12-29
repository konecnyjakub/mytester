<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

use MyTester\ResultsFormatter;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Helper
{
    /** @var array<string, class-string<ResultsFormatter>>  */
    public static array $availableFormatters = [
        "console" => Console::class,
        "junit" => JUnit::class,
        "tap" => Tap::class,
        "testdox" => TestDox::class,
    ];

    private function __construct()
    {
    }

    /**
     * @param string $filename {@see ResultsFormatter::getOutputFileName()}
     */
    public static function isFileOutput(string $filename): bool
    {
        $consoleOutputs = [
            "php://stdout", "php://stderr", "php://output",
        ];
        return !in_array($filename, $consoleOutputs, true);
    }
}
