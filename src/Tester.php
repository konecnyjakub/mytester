<?php
declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;
use Jean85\PrettyVersions;
use MyTester\Bridges\NetteRobotLoader\TestSuitsFinder;
use Nette\Utils\Finder;

/**
 * Automated tests runner
 * 
 * @author Jakub Konečný
 * @property-read string[] $suits
 * @method void onExecute()
 */
final class Tester {
  use \Nette\SmartObject;

  private const PACKAGE_NAME = "konecnyjakub/mytester";
  private const TIMER_NAME = "My Tester";
  
  /** @var string[] */
  private array $suits;
  /** @var callable[] */
  public array $onExecute = [
    Environment::class . "::setup",
  ];
  public ITestSuitFactory $testSuitFactory;
  private string $folder;
  /** @var SkippedTest[] */
  private array $skipped = [];
  private string $results = "";
  
  public function __construct(string $folder) {
    $this->onExecute[] = [$this, "printInfo"];
    $this->suits = (new TestSuitsFinder())->getSuits($folder);
    $this->testSuitFactory = new class implements ITestSuitFactory {
      public function create(string $className): TestCase {
        return new $className();
      }
    };
    $this->folder = $folder;
  }
  
  /**
   * @return string[]
   */
  protected function getSuits(): array {
    return $this->suits;
  }
  
  /**
   * Execute all tests
   */
  public function execute(): void {
    $this->onExecute();
    $failed = false;
    foreach($this->suits as $suit) {
      $suit = $this->testSuitFactory->create($suit[0]);
      if(!$suit->run()) {
        $failed = true;
      }
      $this->saveResults($suit);
    }
    $this->printResults();
    exit((int) $failed);
  }

  /**
   * Print version of My Tester and PHP
   */
  private function printInfo(): void {
    echo "My Tester " . PrettyVersions::getVersion(static::PACKAGE_NAME) . "\n";
    echo "\n";
    echo "PHP " . PHP_VERSION . "(" . PHP_SAPI . ")\n";
    echo "\n";
  }

  private function printResults(): void {
    $results = $this->results;
    echo $results . "\n";
    $this->printSkipped();
    $failed = str_contains($results, TestCase::RESULT_FAILED);
    if(!$failed) {
      echo "\n";
      echo "OK";
    } else {
      static::printFailed();
      echo "\n";
      echo "Failed";
    }
    $resultsLine = " (" . strlen($results) . " tests";
    if($failed) {
      $resultsLine .= ", " . substr_count($results, TestCase::RESULT_FAILED) . " failed";
    }
    if(str_contains($results, TestCase::RESULT_SKIPPED)) {
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
  private function printSkipped(): void {
    foreach($this->skipped as $skipped) {
      $reason = "";
      if($skipped->reason) {
        $reason = ": {$skipped->reason}";
      }
      echo "Skipped $skipped->name$reason\n";
    }
  }

  /**
   * Print info about failed tests
   */
  private function printFailed(): void {
    $filenameSuffix = ".errors";
    $files = Finder::findFiles("*$filenameSuffix")->in($this->folder);
    /** @var \SplFileInfo $file */
    foreach($files as $name => $file) {
      echo "--- " . $file->getBasename($filenameSuffix) . "\n";
      echo file_get_contents($name);
    }
  }

  private function saveResults(TestCase $testCase): void {
    $jobs = $testCase->jobs;
    foreach($jobs as $job) {
      switch($job->result) {
        case Job::RESULT_PASSED:
          $result = TestCase::RESULT_PASSED;
          break;
        case Job::RESULT_SKIPPED:
          $result = TestCase::RESULT_SKIPPED;
          $this->skipped[] = new SkippedTest($job->name, (is_string($job->skip) ? $job->skip : ""));
          break;
        case Job::RESULT_FAILED:
          $result = TestCase::RESULT_FAILED;
          break;
        default:
          $result = "";
          break;
      }
      $this->results .= $result;
    }
  }
}
?>