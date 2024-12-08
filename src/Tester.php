<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventDispatcher;
use Konecnyjakub\EventDispatcher\ListenerProvider;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Automated tests runner
 *
 * @author Jakub Konečný
 */
final readonly class Tester
{
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param ITesterExtension[] $extensions
     */
    public function __construct(
        private TestsFolderProvider $folderProvider,
        public ITestSuitesFinder $testSuitesFinder,
        public ITestSuiteFactory $testSuiteFactory = new TestSuiteFactory(),
        private array $extensions = [],
        private IResultsFormatter $resultsFormatter = new ResultsFormatters\Console(),
        private ConsoleColors $console = new ConsoleColors()
    ) {
        if (is_subclass_of($this->resultsFormatter, IConsoleAwareResultsFormatter::class)) {
            $this->resultsFormatter->setConsole($this->console);
        }
        $this->eventDispatcher = $this->createEventDispatcher();
    }

    private function createEventDispatcher(): EventDispatcherInterface
    {
        $listenerProvider = new ListenerProvider();

        $listenerProvider->registerListener(
            Events\TestsStartedEvent::class,
            function (Events\TestsStartedEvent $event) {
                $this->resultsFormatter->reportTestsStarted($event->testCases);
                foreach ($this->extensions as $extension) {
                    $callbacks = $extension->getEventsPreRun();
                    foreach ($callbacks as $callback) {
                        $callback($event);
                    }
                }
            }
        );

        $listenerProvider->registerListener(
            Events\TestsFinishedEvent::class,
            function (Events\TestsFinishedEvent $event) {
                $this->resultsFormatter->reportTestsFinished($event->testCases);
                $this->resultsFormatter->outputResults((string) getcwd());
                foreach ($this->extensions as $extension) {
                    $callbacks = $extension->getEventsAfterRun();
                    foreach ($callbacks as $callback) {
                        $callback($event);
                    }
                }
            }
        );

        $listenerProvider->registerListener(
            Events\TestCaseStarted::class,
            function (Events\TestCaseStarted $event) {
                $this->resultsFormatter->reportTestCaseStarted($event->testCase);
                foreach ($this->extensions as $extension) {
                    $callbacks = $extension->getEventsBeforeTestCase();
                    foreach ($callbacks as $callback) {
                        $callback($event);
                    }
                }
            }
        );

        $listenerProvider->registerListener(
            Events\TestCaseFinished::class,
            function (Events\TestCaseFinished $event) {
                $this->resultsFormatter->reportTestCaseFinished($event->testCase);
                foreach ($this->extensions as $extension) {
                    $callbacks = $extension->getEventsAfterTestCase();
                    foreach ($callbacks as $callback) {
                        $callback($event);
                    }
                }
            }
        );

        return new EventDispatcher($listenerProvider);
    }

    /**
     * Execute all tests
     */
    public function execute(): never
    {
        $failed = false;

        /** @var TestCase[] $testCases */
        $testCases = [];
        $suites = $this->testSuitesFinder->getSuites($this->folderProvider->folder);
        foreach ($suites as $suite) {
            $testCases[] = $this->testSuiteFactory->create($suite);
        }

        $this->eventDispatcher->dispatch(new Events\TestsStartedEvent($testCases));

        foreach ($testCases as $testCase) {
            $this->eventDispatcher->dispatch(new Events\TestCaseStarted($testCase));
            if (!$testCase->run()) {
                $failed = true;
            }
            $this->eventDispatcher->dispatch(new Events\TestCaseFinished($testCase));
        }

        $this->eventDispatcher->dispatch(new Events\TestsFinishedEvent($testCases));

        exit((int) $failed);
    }
}
