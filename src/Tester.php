<?php
declare(strict_types=1);

namespace MyTester;

use Composer\InstalledVersions;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
use Nette\CommandLine\Console;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Automated tests runner
 *
 * @author Jakub Konečný
 * @property bool $useColors
 */
final class Tester
{
    use \Nette\SmartObject;

    private const PACKAGE_NAME = "konecnyjakub/mytester";

    public ITestSuiteFactory $testSuiteFactory;
    public ITestSuitesFinder $testSuitesFinder;
    private IResultsFormatter $resultsFormatter;
    private Console $console;
    private readonly string $folder;
    private bool $useColors = false;
    /** @var ITesterExtension[] */
    private array $extensions = [];
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param ITesterExtension[] $extensions
     */
    public function __construct(
        string $folder,
        ITestSuitesFinder $testSuitesFinder = null,
        ITestSuiteFactory $testSuiteFactory = new TestSuiteFactory(),
        array $extensions = [],
        ?IResultsFormatter $resultsFormatter = null
    ) {
        if ($testSuitesFinder === null) {
            $testSuitesFinder = new ChainTestSuitesFinder();
            $testSuitesFinder->registerFinder(new ComposerTestSuitesFinder());
            $testSuitesFinder->registerFinder(new TestSuitesFinder());
        }
        $this->testSuitesFinder = $testSuitesFinder;
        $this->testSuiteFactory = $testSuiteFactory;
        $this->folder = $folder;
        $this->console = new Console();
        $this->resultsFormatter = $resultsFormatter ?? new ResultsFormatters\Console();
        if (is_subclass_of($this->resultsFormatter, ITestsFolderAwareResultsFormatter::class)) {
            $this->resultsFormatter->setTestsFolder($this->folder);
        }
        if (is_subclass_of($this->resultsFormatter, IConsoleAwareResultsFormatter::class)) {
            $this->resultsFormatter->setConsole($this->console);
        }
        $this->extensions = $extensions;

        $listenerProvider = new TesterListenerProvider($this->extensions);
        $listenerProvider->registerListener(Events\TestsStartedEvent::class, function () {
            $this->printInfo();
        });
        $listenerProvider->registerListener(Events\TestsStartedEvent::class, [$this->resultsFormatter, "setup"]);
        $listenerProvider->registerListener(
            Events\TestsStartedEvent::class,
            function (Events\TestsStartedEvent $event) {
                $this->resultsFormatter->reportTestsStarted($event->testCases);
            }
        );
        $listenerProvider->registerListener(Events\TestsFinishedEvent::class, function () {
            $this->printResults();
        });
        $listenerProvider->registerListener(
            Events\TestsFinishedEvent::class,
            function (Events\TestsFinishedEvent $event) {
                $this->resultsFormatter->reportTestsFinished($event->testCases);
            }
        );
        $listenerProvider->registerListener(
            Events\TestCaseStarted::class,
            function (Events\TestCaseStarted $event) {
                $this->resultsFormatter->reportTestCaseStarted($event->testCase);
            }
        );
        $listenerProvider->registerListener(
            Events\TestCaseFinished::class,
            function (Events\TestCaseFinished $event) {
                $this->resultsFormatter->reportTestCaseFinished($event->testCase);
            }
        );
        $this->eventDispatcher = new TesterEventDispatcher($listenerProvider);
    }

    protected function isUseColors(): bool
    {
        return $this->useColors;
    }

    protected function setUseColors(bool $useColors): void
    {
        $this->useColors = $useColors;
        $this->console->useColors($useColors);
    }

    /**
     * Execute all tests
     */
    public function execute(): void
    {
        $failed = false;

        /** @var TestCase[] $testCases */
        $testCases = [];
        $suites = $this->testSuitesFinder->getSuites($this->folder);
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

    /**
     * Print version of My Tester and PHP
     */
    private function printInfo(): void
    {
        $version = InstalledVersions::getPrettyVersion(static::PACKAGE_NAME);
        echo $this->console->color("silver", "My Tester $version\n");
        echo "\n";
        echo $this->console->color("silver", "PHP " . PHP_VERSION . "(" . PHP_SAPI . ")\n");
        echo "\n";
    }

    private function printResults(): void
    {
        $filename = $this->resultsFormatter->getOutputFileName((string) getcwd());
        if (ResultsHelper::isFileOutput($filename)) {
            echo "Results are redirected into file $filename\n";
        }

        /** @var resource $outputFile */
        $outputFile = fopen($filename, "w");
        fwrite($outputFile, $this->resultsFormatter->render());
        fclose($outputFile);
    }
}
