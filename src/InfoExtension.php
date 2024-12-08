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

    public function getEventsPreRun(): array
    {
        return [
            [$this, "printInfo"],
        ];
    }

    public function getEventsAfterRun(): array
    {
        return [];
    }

    public function getEventsBeforeTestCase(): array
    {
        return [];
    }

    public function getEventsAfterTestCase(): array
    {
        return [];
    }

    /**
     * Print version of My Tester and PHP
     *
     * @internal
     */
    public function printInfo(): void
    {
        $version = InstalledVersions::getPrettyVersion(static::PACKAGE_NAME);
        echo $this->console->color("My Tester $version\n", "silver");
        echo "\n";
        echo $this->console->color("PHP " . PHP_VERSION . "(" . PHP_SAPI . ")\n", "silver");
        echo "\n";
    }
}
