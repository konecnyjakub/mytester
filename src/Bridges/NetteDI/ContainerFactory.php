<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Nette\Bootstrap\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;

/**
 * @author Jakub Konečný
 */
final class ContainerFactory
{
    use \Nette\StaticClass;

    public static string $tempDir = "";

    /** @var callable|null */
    public static $onCreate = null;

    private static ?Container $container = null;

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
            $configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
                $compiler->addExtension("mytester", new MyTesterExtension());
            };
            if (is_callable(self::$onCreate)) {
                call_user_func_array(self::$onCreate, [$configurator, ]);
            }
            self::$container = $configurator->createContainer();
        }
        return self::$container;
    }
}
