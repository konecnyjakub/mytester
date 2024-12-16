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
        $version = InstalledVersions::getPrettyVersion(static::PACKAGE_NAME);
        echo $this->console->color("My Tester $version\n", "silver");
        echo "\n";
        echo $this->console->color("PHP " . PHP_VERSION . " (" . PHP_SAPI . ")\n", "silver");
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
}
