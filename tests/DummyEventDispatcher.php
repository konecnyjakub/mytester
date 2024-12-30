<?php
declare(strict_types=1);

namespace MyTester;

use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class DummyEventDispatcher implements EventDispatcherInterface
{
    /**
     * @template T of object
     * @param T $event
     * @return T
     */
    public function dispatch(object $event): object
    {
        return $event;
    }
}
