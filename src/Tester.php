<?php

declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;
use Composer\InstalledVersions;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\PcovEngine;
use MyTester\CodeCoverage\PercentFormatter;
use MyTester\CodeCoverage\PhpdbgEngine;
use MyTester\CodeCoverage\XDebugEngine;
use Nette\CommandLine\Console;
use Nette\Utils\Finder;

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
    private Console $console;
    private string $folder;
    private bool $useColors = false;
    /** @var SkippedTest[] */
    private array $skipped = [];
    private string $results = "";
    private Collector $codeCoverageCollector;

    public function __construct(
        string $folder,
        ITestSuitesFinder $testSuitesFinder = null,
        ITestSuiteFactory $testSuiteFactory = null
    ) {
        $this->onExecute[] = [$this, "setup"];
        $this->onExecute[] = [$this, "printInfo"];
        $this->onFinish[] = [$this, "printResults"];
        $this->onFinish[] = [$this, "reportCodeCoverage"];
        if ($testSuitesFinder === null) {
            $testSuitesFinder = new ChainTestSuitesFinder();
            $testSuitesFinder->registerFinder(new ComposerTestSuitesFinder());
            $testSuitesFinder->registerFinder(new TestSuitesFinder());
        }
        $this->testSuitesFinder = $testSuitesFinder;
        $this->testSuiteFactory = $testSuiteFactory ?? new TestSuiteFactory();
        $this->folder = $folder;
        $this->console = new Console();
        $this->codeCoverageCollector = new Collector();
        $this->codeCoverageCollector->registerEngine(new PcovEngine());
        $this->codeCoverageCollector->registerEngine(new PhpdbgEngine());
        $this->codeCoverageCollector->registerEngine(new XDebugEngine());
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
            $this->saveResults($suite);
        }
        $this->onFinish();
        exit((int) $failed);
    }

    private function setup(): void
    {
        Timer::start(static::TIMER_NAME);
        try {
            $this->codeCoverageCollector->start();
        } catch (CodeCoverageException $e) {
            if ($e->getCode() !== CodeCoverageException::NO_ENGINE_AVAILABLE) {
                throw $e;
            }
        }
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
        $results = $this->results;
        $rf = TestCase::RESULT_FAILED;
        $rs = TestCase::RESULT_SKIPPED;
        $results = str_replace($rf, $this->console->color("red", $rf), $results);
        $results = str_replace($rs, $this->console->color("yellow", $rs), $results);
        echo $results . "\n";
        $results = $this->results;
        $this->printSkipped();
        $failed = str_contains($results, TestCase::RESULT_FAILED);
        if (!$failed) {
            echo "\n";
            $resultsLine = "OK";
        } else {
            $this->printFailed();
            echo "\n";
            $resultsLine = "Failed";
        }
        $resultsLine .= " (" . strlen($results) . " tests";
        if ($failed) {
            $resultsLine .= ", " . substr_count($results, TestCase::RESULT_FAILED) . " failed";
        }
        if (str_contains($results, TestCase::RESULT_SKIPPED)) {
            $resultsLine .= ", " . substr_count($results, TestCase::RESULT_SKIPPED) . " skipped";
        }
        Timer::stop(static::TIMER_NAME);
        $time = Timer::read(static::TIMER_NAME, Timer::FORMAT_HUMAN);
        $resultsLine .= ", $time)";
        $resultsLine = $this->console->color((!$failed) ? "green" : "red", $resultsLine);
        echo $resultsLine . "\n";
    }

    /**
     * Print info about skipped tests
     */
    private function printSkipped(): void
    {
        foreach ($this->skipped as $skipped) {
            $reason = "";
            if ($skipped->reason) {
                $reason = ": {$skipped->reason}";
            }
            echo "Skipped $skipped->name$reason\n";
        }
    }

    /**
     * Print info about failed tests
     */
    private function printFailed(): void
    {
        $filenameSuffix = ".errors";
        $files = Finder::findFiles("*$filenameSuffix")->in($this->folder);
        /** @var \SplFileInfo $file */
        foreach ($files as $name => $file) {
            echo "--- " . $file->getBasename($filenameSuffix) . "\n";
            echo file_get_contents($name);
        }
    }

    private function saveResults(TestCase $testCase): void
    {
        $jobs = $testCase->jobs;
        foreach ($jobs as $job) {
            switch ($job->result) {
                case Job::RESULT_PASSED:
                    $result = TestCase::RESULT_PASSED;
                    break;
                case Job::RESULT_SKIPPED:
                    $result = TestCase::RESULT_SKIPPED;
                    $this->skipped[] = new SkippedTest($job->name, (is_string($job->skip) ? $job->skip : ""));
                    break;
                case Job::RESULT_FAILED:
                    $result = TestCase::RESULT_FAILED;
                    $output = $job->output;
                    if (strlen($output) > 0) {
                        file_put_contents("$this->folder/$job->name.errors", $output);
                    }
                    break;
                default:
                    $result = "";
                    break;
            }
            $this->results .= $result;
        }
    }

    private function reportCodeCoverage(): void
    {
        try {
            $coverageData = $this->codeCoverageCollector->finish();
        } catch (CodeCoverageException $e) {
            if ($e->getCode() === CodeCoverageException::COLLECTOR_NOT_STARTED) {
                return;
            }
            throw $e;
        }

        $percentFormatter = new PercentFormatter();
        echo $percentFormatter->render($coverageData);
    }
}
