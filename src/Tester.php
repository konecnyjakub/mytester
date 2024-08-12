<?php
declare(strict_types=1);

namespace MyTester;

use Composer\InstalledVersions;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
use Nette\CommandLine\Console;

/**
 * Automated tests runner
 *
 * @author Jakub Konečný
 * @property bool $useColors
 * @method void onExecute()
 * @method void onFinish()
 */
final class Tester
{
    use \Nette\SmartObject;

    private const PACKAGE_NAME = "konecnyjakub/mytester";

    /** @var callable[] */
    public array $onExecute = [];
    /** @var callable[] */
    public array $onFinish = [];
    public ITestSuiteFactory $testSuiteFactory;
    public ITestSuitesFinder $testSuitesFinder;
    private IResultsFormatter $resultsFormatter;
    private Console $console;
    private readonly string $folder;
    private bool $useColors = false;
    /** @var ITesterExtension[] */
    private array $extensions = [];

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

        $this->onExecute[] = [$this, "printInfo"];
        $this->onExecute[] = [$this->resultsFormatter, "setup"];
        $this->onExecute[] = function () {
            foreach ($this->extensions as $extension) {
                foreach ($extension->getEventsPreRun() as $callback) {
                    $callback();
                }
            }
        };

        $this->onFinish[] = [$this, "printResults"];
        $this->onFinish[] = function () {
            foreach ($this->extensions as $extension) {
                foreach ($extension->getEventsAfterRun() as $callback) {
                    $callback();
                }
            }
        };
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
        $this->onExecute();
        $failed = false;
        /** @var TestCase[] $testCases */
        $testCases = [];
        $suites = $this->testSuitesFinder->getSuites($this->folder);
        foreach ($suites as $suite) {
            $testCases[] = $this->testSuiteFactory->create($suite);
        }
        $this->resultsFormatter->reportTestsStarted($testCases);
        foreach ($testCases as $testCase) {
            $this->resultsFormatter->reportTestCaseStarted($testCase);
            if (!$testCase->run()) {
                $failed = true;
            }
            $this->resultsFormatter->reportTestCaseFinished($testCase);
        }
        $this->resultsFormatter->reportTestsFinished($testCases);
        $this->onFinish();
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
