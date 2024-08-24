<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Nette\DI\Container;

/**
 * @author Jakub Konečný
 * @internal
 */
trait TCompiledContainer
{
    protected function getContainer(): Container
    {
        return ContainerFactory::create(false);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    protected function getService(string $class): object
    {
        return $this->getContainer()->getByType($class);
    }

    protected function refreshContainer(array $config = []): Container
    {
        return ContainerFactory::create(true, $config);
    }
}