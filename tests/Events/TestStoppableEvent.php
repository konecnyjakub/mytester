<?php
declare(strict_types=1);

namespace MyTester\Events;

use Psr\EventDispatcher\StoppableEventInterface;

final class TestStoppableEvent implements StoppableEventInterface
{
    public bool $stopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}
