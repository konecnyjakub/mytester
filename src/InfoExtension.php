<?php
declare(strict_types=1);

namespace MyTester;

use Composer\InstalledVersions;

final class InfoExtension implements ITesterExtension
{
    private const string PACKAGE_NAME = "konecnyjakub/mytester";

    public function __construct(private readonly ConsoleColors $console)
    {
    }

    public function onTestsStarted(Events\TestsStarted $event): void
    {
        echo $this->console->color(self::getTesterVersion() . "\n", "silver");
        echo "\n";
        echo $this->console->color(self::getPhpVersion() . "\n", "silver");
        echo "\n";
    }

    public function onTestsFinished(Events\TestsFinished $event): void
    {
    }

    public function onTestCaseStarted(Events\TestCaseStarted $event): void
    {
    }

    public function onTestCaseFinished(Events\TestCaseFinished $event): void
    {
    }

    public static function getTesterVersion(): string
    {
        $version = InstalledVersions::getPrettyVersion(self::PACKAGE_NAME);
        return "My Tester $version";
    }

    public static function getPhpVersion(): string
    {
        return "PHP " . PHP_VERSION . " (" . PHP_SAPI . ")";
    }
}
