<?php
declare(strict_types=1);

namespace MyTester;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Listener provider for {@see Tester}
 *
 * @author Jakub Konečný
 * @internal
 */
final class TesterListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<class-string, callable[]>
     */
    private array $listeners = [];

    /**
     * @param ITesterExtension[] $extensions
     */
    public function __construct(private array $extensions)
    {
    }

    /**
     * @param class-string $className
     */
    public function registerListener(string $className, callable $callback): void
    {
        if (!array_key_exists($className, $this->listeners)) {
            $this->listeners[$className] = [];
        }
        $this->listeners[$className][] = $callback;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $listeners = $this->listeners[$event::class] ?? [];
        foreach ($this->extensions as $extension) {
            switch ($event::class) {
                case Events\TestsStartedEvent::class:
                    $listeners = array_merge($listeners, $extension->getEventsPreRun());
                    break;
                case Events\TestsFinishedEvent::class:
                    $listeners = array_merge($listeners, $extension->getEventsAfterRun());
                    break;
            }
        }
        return $listeners;
    }
}
