<?php
declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;
use Composer\InstalledVersions;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use Nette\CommandLine\Console;

/**
 * Automated tests runner
 *
 * @author Jakub Konečný
 * @property-read string[] $suites
 * @property bool $useColors
 * @method void onExecute()
 * @method void onFinish()
 */
final class Tester
{
    use \Nette\SmartObject;

    private const PACKAGE_NAME = "konecnyjakub/mytester";
    private const TIMER_NAME = "My Tester";

    /** @var string[] */
    private array $suites = [];
    /** @var callable[] */
    public array $onExecute = [];
    /** @var callable[] */
    public array $onFinish = [];
    public ITestSuiteFactory $testSuiteFactory;
    public ITestSuitesFinder $testSuitesFinder;
    private IOutputFormatter $outputFormatter;
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
        array $extensions = []
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
        $this->outputFormatter = new \MyTester\OutputFormatters\Console($this->console, $this->folder);
        $this->extensions = $extensions;

        $this->onExecute[] = [$this, "setup"];
        $this->onExecute[] = [$this, "printInfo"];
        $this->onExecute[] = [$this->outputFormatter, "setup"];
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

    /**
     * @return string[]
     */
    protected function getSuites(): array
    {
        if (count($this->suites) === 0) {
            $this->suites = $this->testSuitesFinder->getSuites($this->folder);
        }
        return $this->suites;
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
        foreach ($this->getSuites() as $suite) {
            $suite = $this->testSuiteFactory->create($suite);
            if (!$suite->run()) {
                $failed = true;
            }
            $this->outputFormatter->reportTestCase($suite);
        }
        $this->onFinish();
        exit((int) $failed);
    }

    private function setup(): void
    {
        Timer::start(static::TIMER_NAME);
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
        Timer::stop(static::TIMER_NAME);
        // @phpstan-ignore argument.type
        $totalTime = (int) Timer::read(static::TIMER_NAME, Timer::FORMAT_PRECISE);
        /** @var resource $outputFile */
        $outputFile = fopen($this->outputFormatter->getOutputFileName((string) getcwd()), "w");
        fwrite($outputFile, $this->outputFormatter->render($totalTime));
        fclose($outputFile);
    }
}
