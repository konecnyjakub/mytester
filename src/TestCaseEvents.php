<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventSubscriber;
use Konecnyjakub\EventDispatcher\Listener;

/**
 * Event subscriber for {@see TestCase}
 *
 * @author Jakub Konečný
 */
final class TestCaseEvents implements EventSubscriber
{
    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\TestSuiteStarted::class => [
                ["onTestSuiteStarted", ],
            ],
            Events\TestSuiteFinished::class => [
                ["onTestSuiteFinished", ],
            ],
        ];
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestSuiteStarted(Events\TestSuiteStarted $event): void
    {
        $event->testSuite->startUp();
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestSuiteFinished(Events\TestSuiteFinished $event): void
    {
        $event->testSuite->shutDown();
    }
}
