<?php
declare(strict_types=1);

namespace MyTester;

use Konecnyjakub\EventDispatcher\AutoListenerProvider;
use Konecnyjakub\EventDispatcher\EventDispatcher;
use Konecnyjakub\EventDispatcher\Listener;
use MyTester\ResultsFormatters\Console;
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
     * @param TesterExtension[] $extensions
     * @param ResultsFormatter[] $resultsFormatters
     */
    public function __construct(
        private TestSuitesSelectionCriteria $testSuitesSelectionCriteria,
        public TestSuitesFinder $testSuitesFinder,
        public TestSuiteFactory $testSuiteFactory = new SimpleTestSuiteFactory(),
        private array $extensions = [],
        private array $resultsFormatters = [new Console(),],
        private ConsoleColors $console = new ConsoleColors()
    ) {
        foreach ($this->resultsFormatters as $resultsFormatter) {
            if (is_subclass_of($resultsFormatter, ConsoleAwareResultsFormatter::class)) {
                $resultsFormatter->setConsole($this->console);
            }
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

        foreach ($this->resultsFormatters as $resultsFormatter) {
            $listenerProvider->addSubscriber($resultsFormatter);
        }

        $listenerProvider->addSubscriber(new TestCaseEvents());

        $listenerProvider->addSubscriber(new JobEvents());

        $listenerProvider->addListener(static function (Events\DeprecationTriggered $event): void {
            printf("Warning: deprecated \"%s\" on %s:%d", $event->message, $event->fileName, $event->fileLine);
        });

        $listenerProvider->addListener(
            #[Listener(priority: AutoListenerProvider::PRIORITY_HIGH - 1)]
            function (Events\TestsFinished $event): void {
                foreach ($this->resultsFormatters as $resultsFormatter) {
                    $resultsFormatter->outputResults((string) getcwd());
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
        $this->eventDispatcher->dispatch(new Events\RunnerStarted());
        $failed = false;

        /** @var TestCase[] $testSuites */
        $testSuites = [];
        $suites = $this->testSuitesFinder->getSuites($this->testSuitesSelectionCriteria);
        foreach ($suites as $suite) {
            $testSuite = $this->testSuiteFactory->create($suite);
            if ($testSuite === null) {
                throw new TestSuiteNotCreatedException("Test suite " . $suite . " could not be created.");
            }
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
