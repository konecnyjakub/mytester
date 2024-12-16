<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\IEventSubscriber;

/**
 * Event subscriber for {@see Tester} extensions
 *
 * @author Jakub Konečný
 * @internal
 */
final readonly class ExtensionsEventSubscriber implements IEventSubscriber
{
    /**
     * @param ITesterExtension[] $extensions
     */
    public function __construct(private array $extensions = [])
    {
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\TestsStartedEvent::class => [
                ["onTestsStarted", ],
            ],
            Events\TestsFinishedEvent::class => [
                ["onTestsFinished", ],
            ],
            Events\TestCaseStarted::class => [
                ["onTestCaseStarted", ],
            ],
            Events\TestCaseFinished::class => [
                ["onTestCaseFinished", ],
            ],
        ];
    }

    public function onTestsStarted(Events\TestsStartedEvent $event): void
    {
        foreach ($this->extensions as $extension) {
            $callbacks = $extension->getEventsPreRun();
            foreach ($callbacks as $callback) {
                $callback($event);
            }
        }
    }

    public function onTestsFinished(Events\TestsFinishedEvent $event): void
    {
        foreach ($this->extensions as $extension) {
            $callbacks = $extension->getEventsAfterRun();
            foreach ($callbacks as $callback) {
                $callback($event);
            }
        }
    }

    public function onTestCaseStarted(Events\TestCaseStarted $event): void
    {
        foreach ($this->extensions as $extension) {
            $callbacks = $extension->getEventsBeforeTestCase();
            foreach ($callbacks as $callback) {
                $callback($event);
            }
        }
    }

    public function onTestCaseFinished(Events\TestCaseFinished $event): void
    {
        foreach ($this->extensions as $extension) {
            $callbacks = $extension->getEventsAfterTestCase();
            foreach ($callbacks as $callback) {
                $callback($event);
            }
        }
    }
}