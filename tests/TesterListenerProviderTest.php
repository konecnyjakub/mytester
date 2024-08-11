<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Attributes\TestSuite;

/**
 * Test suite for class TesterListenerProvider
 *
 * @author Jakub Konečný
 */
#[TestSuite("TesterListenerProvider")]
final class TesterListenerProviderTest extends TestCase
{
    public function testGetListenersForEvent(): void
    {
        $listenerProvider = new TesterListenerProvider([]);
        $this->assertSame([], $listenerProvider->getListenersForEvent(new Events\TestsStartedEvent([])));
        $this->assertSame([], $listenerProvider->getListenersForEvent(new Events\TestsFinishedEvent([])));
        $this->assertSame([], $listenerProvider->getListenersForEvent(new \stdClass()));

        $extensions = [
            new class implements ITesterExtension
            {
                public function getEventsPreRun(): array
                {
                    return ["time", ];
                }

                public function getEventsAfterRun(): array
                {
                    return ["pi", ];
                }
            }
        ];
        $listenerProvider = new TesterListenerProvider($extensions);
        $this->assertSame(["time", ], $listenerProvider->getListenersForEvent(new Events\TestsStartedEvent([])));
        $this->assertSame(["pi", ], $listenerProvider->getListenersForEvent(new Events\TestsFinishedEvent([])));
        $this->assertSame([], $listenerProvider->getListenersForEvent(new \stdClass()));

        $listenerProvider = new TesterListenerProvider([]);
        $listenerProvider->registerListener(Events\TestsStartedEvent::class, "time");
        $listenerProvider->registerListener(Events\TestsFinishedEvent::class, "pi");
        $this->assertSame(["time", ], $listenerProvider->getListenersForEvent(new Events\TestsStartedEvent([])));
        $this->assertSame(["pi", ], $listenerProvider->getListenersForEvent(new Events\TestsFinishedEvent([])));
        $this->assertSame([], $listenerProvider->getListenersForEvent(new \stdClass()));

        $listenerProvider = new TesterListenerProvider($extensions);
        $listenerProvider->registerListener(Events\TestsStartedEvent::class, "pi");
        $listenerProvider->registerListener(Events\TestsFinishedEvent::class, "time");
        $this->assertSame(["pi", "time", ], $listenerProvider->getListenersForEvent(new Events\TestsStartedEvent([])));
        $this->assertSame(["time", "pi", ], $listenerProvider->getListenersForEvent(new Events\TestsFinishedEvent([])));
        $this->assertSame([], $listenerProvider->getListenersForEvent(new \stdClass()));
    }
}
