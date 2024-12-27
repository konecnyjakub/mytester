<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\IEventSubscriber;
use Konecnyjakub\EventDispatcher\Listener;

/**
 * @author Jakub Konečný
 * @internal
 */
final readonly class ResultsFormatterEventSubscriber implements IEventSubscriber
{
    public function __construct(private IResultsFormatter $resultsFormatter)
    {
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            Events\TestsStarted::class => [
                ["reportTestsStarted", ],
            ],
            Events\TestsFinished::class => [
                ["reportTestsFinished", ],
            ],
            Events\TestCaseStarted::class => [
                ["reportTestCaseStarted", ],
            ],
            Events\TestCaseFinished::class => [
                ["reportTestCaseFinished", ],
            ],
        ];
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function reportTestsStarted(Events\TestsStarted $event): void
    {
        $this->resultsFormatter->reportTestsStarted($event->testCases);
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function reportTestsFinished(Events\TestsFinished $event): void
    {
        $this->resultsFormatter->reportTestsFinished($event->testCases);
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function reportTestCaseStarted(Events\TestCaseStarted $event): void
    {
        $this->resultsFormatter->reportTestCaseStarted($event->testCase);
    }

    #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
    public function reportTestCaseFinished(Events\TestCaseFinished $event): void
    {
        $this->resultsFormatter->reportTestCaseFinished($event->testCase);
    }
}
