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
            Events\TestStarted::class => [
                ["onTestStarted", ],
            ],
            Events\TestFinished::class => [
                ["onTestFinished", ],
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

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestStarted(Events\TestStarted $event): void
    {
        $callback = $event->test->callback;
        if (is_array($callback) && isset($callback[0]) && $callback[0] instanceof TestCase) {
            $callback[0]->setUp();
        }
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function onTestFinished(Events\TestFinished $event): void
    {
        $callback = $event->test->callback;
        if (is_array($callback) && isset($callback[0]) && $callback[0] instanceof TestCase) {
            $callback[0]->tearDown();
        }
    }
}
