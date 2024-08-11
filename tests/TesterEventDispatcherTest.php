<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class TesterEventDispatcher
 *
 * @author Jakub Konečný
 */
#[TestSuite("TesterEventDispatcher")]
final class TesterEventDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        $event = new Events\TestsStartedEvent([]);
        $var = 0;
        $listenerProvider = new TesterListenerProvider([]);
        $listenerProvider->registerListener($event::class, function () use (&$var) {
            $var++;
        });
        $eventDispatcher = new TesterEventDispatcher($listenerProvider);
        $this->assertSame($event, $eventDispatcher->dispatch($event));
        $this->assertSame(1, $var);

        $event = new Events\TestStoppableEvent();
        $var = 0;
        $listenerProvider = new TesterListenerProvider([]);
        $listenerProvider->registerListener($event::class, function (Events\TestStoppableEvent $event) use (&$var) {
            $var++;
            $event->stopped = true;
        });
        $listenerProvider->registerListener($event::class, function (Events\TestStoppableEvent $event) use (&$var) {
            $var++;
        });
        $eventDispatcher = new TesterEventDispatcher($listenerProvider);
        $this->assertSame($event, $eventDispatcher->dispatch($event));
        $this->assertSame(1, $var);
        $this->assertTrue($event->stopped);
    }
}
