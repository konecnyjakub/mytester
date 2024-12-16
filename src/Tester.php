<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\EventDispatcher;
use Konecnyjakub\EventDispatcher\PriorityListenerProvider;
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
        $listenerProvider = new PriorityListenerProvider();

        $listenerProvider->addSubscriber(new ExtensionsEventSubscriber($this->extensions));

        $listenerProvider->addListener(
            Events\TestsStarted::class,
            function (Events\TestsStarted $event) {
                $this->resultsFormatter->reportTestsStarted($event->testCases);
            },
            100
        );

        $listenerProvider->addListener(
            Events\TestsFinished::class,
            function (Events\TestsFinished $event) {
                $this->resultsFormatter->reportTestsFinished($event->testCases);
            },
            100
        );
        $listenerProvider->addListener(
            Events\TestsFinished::class,
            function (Events\TestsFinished $event) {
                $this->resultsFormatter->outputResults((string) getcwd());
            },
            99
        );

        $listenerProvider->addListener(
            Events\TestCaseStarted::class,
            function (Events\TestCaseStarted $event) {
                $this->resultsFormatter->reportTestCaseStarted($event->testCase);
            },
            100
        );

        $listenerProvider->addListener(
            Events\TestCaseFinished::class,
            function (Events\TestCaseFinished $event) {
                $this->resultsFormatter->reportTestCaseFinished($event->testCase);
            },
            100
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
