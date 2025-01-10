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
        public ITestSuiteFactory $testSuiteFactory = new SimpleTestSuiteFactory(),
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

        foreach ($this->extensions as $extension) {
            $listenerProvider->addSubscriber($extension);
        }

        $listenerProvider->addSubscriber($this->resultsFormatter);

        $listenerProvider->addListener(
            #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH - 1)]
            function (Events\TestsFinished $event): void {
                $this->resultsFormatter->outputResults((string) getcwd());
            }
        );

        return new EventDispatcher($listenerProvider);
    }

    /**
     * Execute all tests
     */
    public function execute(): never
    {
        $this->eventDispatcher->dispatch(new Events\RunnerStarted());
        $failed = false;

        /** @var TestCase[] $testSuites */
        $testSuites = [];
        $suites = $this->testSuitesFinder->getSuites($this->folderProvider->folder);
        foreach ($suites as $suite) {
            $testSuite = $this->testSuiteFactory->create($suite);
            $testSuite->setEventDispatcher($this->eventDispatcher);
            $testSuites[] = $testSuite;
        }

        $this->eventDispatcher->dispatch(new Events\TestsStarted($testSuites));
        foreach ($testSuites as $testSuite) {
            if (!$testSuite->run()) {
                $failed = true;
            }
        }
        $this->eventDispatcher->dispatch(new Events\TestsFinished($testSuites));

        $this->eventDispatcher->dispatch(new Events\RunnerFinished());
        exit((int) $failed);
    }
}
