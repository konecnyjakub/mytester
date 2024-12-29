<?php
declare(strict_types=1);

namespace MyTester;

use Composer\InstalledVersions;

final class InfoExtension implements ITesterExtension
{
    private const string PACKAGE_NAME = "konecnyjakub/mytester";

    /** @var string[] */
    private array $extensionNames = [];

    public function __construct(private readonly ConsoleColors $console)
    {
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\ExtensionsLoaded::class => [
                ["onExtensionsLoaded", ],
            ],
            Events\RunnerStarted::class => [
                ["printInfo", ],
            ],
        ];
    }

    public function onExtensionsLoaded(Events\ExtensionsLoaded $event): void
    {
        $this->extensionNames = array_map(function (ITesterExtension $extension) {
            return $extension->getName();
        }, $event->extensions);
    }

    public function printInfo(Events\RunnerStarted $event): void
    {
        echo $this->console->color(self::getTesterVersion() . "\n", "silver");
        echo $this->console->color("Loaded extensions: " . implode(", ", $this->extensionNames) . "\n", "silver");
        echo "\n";
        echo $this->console->color(self::getPhpVersion() . "\n", "silver");
        echo "\n";
    }

    public function getName(): string
    {
        return "info";
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
