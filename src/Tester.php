<?php

declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;
use Jean85\PrettyVersions;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use Nette\Utils\Finder;

/**
 * Automated tests runner
 *
 * @author Jakub Konečný
 * @property-read string[] $suites
 * @method void onExecute()
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
    public ITestSuiteFactory $testSuiteFactory;
    public ITestsSuitesFinder $testsSuitesFinder;
    private string $folder;
    /** @var SkippedTest[] */
    private array $skipped = [];
    private string $results = "";

    public function __construct(
        string $folder,
        ITestsSuitesFinder $testsSuitesFinder = null,
        ITestSuiteFactory $testSuiteFactory = null
    ) {
        $this->onExecute[] = [$this, "setup"];
        $this->onExecute[] = [$this, "printInfo"];
        $this->testsSuitesFinder = $testsSuitesFinder ?? new TestSuitesFinder();
        $this->testSuiteFactory = $testSuiteFactory ?? new class implements ITestSuiteFactory
        {
            public function create(string $className): TestCase
            {
                return new $className();
            }
        };
        $this->folder = $folder;
    }

    /**
     * @return string[]
     */
    protected function getSuites(): array
    {
        if (count($this->suites) === 0) {
            $this->suites = $this->testsSuitesFinder->getSuites($this->folder);
        }
        return $this->suites;
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
        $this->printResults();
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
        echo "My Tester " . PrettyVersions::getVersion(static::PACKAGE_NAME) . "\n";
        echo "\n";
        echo "PHP " . PHP_VERSION . "(" . PHP_SAPI . ")\n";
        echo "\n";
    }

    private function printResults(): void
    {
        $results = $this->results;
        echo $results . "\n";
        $this->printSkipped();
        $failed = str_contains($results, TestCase::RESULT_FAILED);
        if (!$failed) {
            echo "\n";
            echo "OK";
        } else {
            $this->printFailed();
            echo "\n";
            echo "Failed";
        }
        $resultsLine = " (" . strlen($results) . " tests";
        if ($failed) {
            $resultsLine .= ", " . substr_count($results, TestCase::RESULT_FAILED) . " failed";
        }
        if (str_contains($results, TestCase::RESULT_SKIPPED)) {
            $resultsLine .= ", " . substr_count($results, TestCase::RESULT_SKIPPED) . " skipped";
        }
        Timer::stop(static::TIMER_NAME);
        $time = Timer::read(static::TIMER_NAME, Timer::FORMAT_HUMAN);
        $resultsLine .= ", $time)";
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
}
