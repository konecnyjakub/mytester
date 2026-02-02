<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Closure;
use Nette\Bootstrap\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;

/**
 * @author Jakub Konečný
 */
final class ContainerFactory
{
    public static string $tempDir = "";

    public static ?Closure $onCreate = null;

    private static ?Container $container = null;

    private function __construct()
    {
    }

    /**
     * @param mixed[] $config
     */
    public static function create(bool $new = false, array $config = []): Container
    {
        if (self::$container === null || $new) {
            $configurator = new Configurator();
            $configurator->addStaticParameters($config);
            $configurator->setDebugMode(true);
            if (self::$tempDir !== "") {
                $configurator->setTempDirectory(self::$tempDir);
            }
            $configurator->onCompile[] = static function (Configurator $configurator, Compiler $compiler): void {
                $compiler->addExtension("mytester", new MyTesterExtension());
            };
            if (self::$onCreate !== null) {
                call_user_func_array(self::$onCreate, [$configurator, ]);
            }
            self::$container = $configurator->createContainer();
        }
        return self::$container;
    }
}
