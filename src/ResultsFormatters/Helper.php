<?php
declare(strict_types=1);

namespace MyTester\ResultsFormatters;

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
}
