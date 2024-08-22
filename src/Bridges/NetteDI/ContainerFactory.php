<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Nette\Bootstrap\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ContainerFactory
{
    use \Nette\StaticClass;

    public static string $tempDir = "";

    /** @var callable|null */
    public static $onCreate = null;

    private static ?Container $container = null;

    public static function create(bool $new = false, array $config = []): Container
    {
        if (static::$container === null || $new) {
            $configurator = new Configurator();
            $configurator->addStaticParameters($config);
            $configurator->setDebugMode(true);
            if (static::$tempDir !== "") {
                $configurator->setTempDirectory(static::$tempDir);
            }
            $configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
                $compiler->addExtension("mytester", new MyTesterExtension());
            };
            if (is_callable(static::$onCreate)) {
                call_user_func_array(static::$onCreate, [$configurator, ]);
            }
            static::$container = $configurator->createContainer();
        }
        return static::$container;
    }
}
