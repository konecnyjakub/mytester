<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventDispatcher;
use Konecnyjakub\EventDispatcher\Listener;
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
        $this->eventDispatcher->dispatch(new Events\ExtensionsLoaded($this->extensions));
    }

    private function createEventDispatcher(): EventDispatcherInterface
    {
        $listenerProvider = new AutoListenerProvider();

        $listenerProvider->addSubscriber(new ExtensionsEventSubscriber($this->extensions));

        $listenerProvider->addListener(
            #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
            function (Events\TestsStarted $event): void {
                $this->resultsFormatter->reportTestsStarted($event->testCases);
            }
        );

        $listenerProvider->addListener(
            #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
            function (Events\TestsFinished $event): void {
                $this->resultsFormatter->reportTestsFinished($event->testCases);
            }
        );
        $listenerProvider->addListener(
            #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH - 1)]
            function (Events\TestsFinished $event): void {
                $this->resultsFormatter->outputResults((string) getcwd());
            }
        );

        $listenerProvider->addListener(
            #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
            function (Events\TestCaseStarted $event): void {
                $this->resultsFormatter->reportTestCaseStarted($event->testCase);
            }
        );

        $listenerProvider->addListener(
            #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH)]
            function (Events\TestCaseFinished $event): void {
                $this->resultsFormatter->reportTestCaseFinished($event->testCase);
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

        $this->eventDispatcher->dispatch(new Events\TestsStarted($testCases));

        foreach ($testCases as $testCase) {
            $this->eventDispatcher->dispatch(new Events\TestCaseStarted($testCase));
            if (!$testCase->run()) {
                $failed = true;
            }
            $this->eventDispatcher->dispatch(new Events\TestCaseFinished($testCase));
        }

        $this->eventDispatcher->dispatch(new Events\TestsFinished($testCases));

        exit((int) $failed);
    }
}
