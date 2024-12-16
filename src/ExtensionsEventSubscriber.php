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
            Events\TestsStarted::class => [
                ["onTestsStarted", ],
            ],
            Events\TestsFinished::class => [
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

    public function onTestsStarted(Events\TestsStarted $event): void
    {
        foreach ($this->extensions as $extension) {
            $extension->onTestsStarted($event);
        }
    }

    public function onTestsFinished(Events\TestsFinished $event): void
    {
        foreach ($this->extensions as $extension) {
            $extension->onTestsFinished($event);
        }
    }

    public function onTestCaseStarted(Events\TestCaseStarted $event): void
    {
        foreach ($this->extensions as $extension) {
            $extension->onTestCaseStarted($event);
        }
    }

    public function onTestCaseFinished(Events\TestCaseFinished $event): void
    {
        foreach ($this->extensions as $extension) {
            $extension->onTestCaseFinished($event);
        }
    }
}
