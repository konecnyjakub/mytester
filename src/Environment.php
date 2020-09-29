<?php
declare(strict_types=1);

namespace MyTester;

use Ayesh\PHP_Timer\Timer;
use Jean85\PrettyVersions;
use Nette\Utils\Finder;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 */
final class Environment {
  use \Nette\StaticClass;

  /** @deprecated */
  public const NAME = "My Tester";
  private const PACKAGE_NAME = "konecnyjakub/mytester";
  private const TIMER_NAME = self::NAME;
  /** @deprecated Use {@see PrettyVersions::getVersion()} */
  public const VERSION = "2.1.0-dev";

  private static int $taskCount = 0;
  private static bool $set = false;
  /** @var SkippedTest[] */
  private static array $skipped = [];
  /** @deprecated */
  public static string $currentJob = "";
  /** @deprecated */
  public static bool $shouldFail = false;
  private static string $results = "";
  
  /**
   * Prints result of a test
   */
  public static function testResult(string $text, bool $success = true): void {
    static::incCounter();
    if($success) {
      return;
    }
    echo "Test " . static::$taskCount . " failed. $text\n";
  }
  
  /**
   * Checks if environment was set
   */
  public static function isSetUp(): bool {
    return static::$set;
  }
  
  /**
   * Increases task counter
   *
   * @internal
   */
  public static function incCounter(): void {
    static::$taskCount++;
  }
  
  /**
   * Resets task counter
   *
   * @internal
   */
  public static function resetCounter(): void {
    static::$taskCount = 0;
  }
  
  public static function getCounter(): int {
    return static::$taskCount;
  }
  
  /**
   * Prints entered text with correct line ending
   *
   * @deprecated Just use echo
   */
  public static function printLine(string $text = ""): void {
    echo "$text\n";
  }

  /**
   * @internal
   */
  public static function addResult(string $result): void {
    static::$results .= $result;
  }

  /**
   * @internal
   */
  public static function addSkipped(string $jobName, string $reason = ""): void {
    static::$skipped[] = new SkippedTest($jobName, $reason);
  }

  /**
   * @return SkippedTest[]
   */
  public static function getSkipped(): array {
    return static::$skipped;
  }

  /**
   * @deprecated Access the property directly
   */
  public static function getShouldFail(): bool {
    return static::$shouldFail;
  }

  /**
   * @deprecated Access the property directly
   */
  public static function setShouldFail(bool $value): void {
    static::$shouldFail = $value;
  }
  
  /**
   * Print version of My Tester and PHP
   */
  public static function printInfo(): void {
    echo "My Tester " . PrettyVersions::getVersion(static::PACKAGE_NAME) . "\n";
    echo "\n";
    echo "PHP " . PHP_VERSION . "(" . PHP_SAPI . ")\n";
    echo "\n";
  }

  public static function printResults(): void {
    $results = static::$results;
    echo $results . "\n";
    static::printSkipped();
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
  private static function printSkipped(): void {
    foreach(static::getSkipped() as $skipped) {
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
  private static function printFailed(): void {
    $filenameSuffix = ".errors";
    $files = Finder::findFiles("*$filenameSuffix")->in(\getTestsDirectory());
    /** @var \SplFileInfo $file */
    foreach($files as $name => $file) {
      echo "--- " . $file->getBasename($filenameSuffix) . "\n";
      echo file_get_contents($name);
    }
  }
  
  /**
   * Sets up the environment
   */
  public static function setup(): void {
    if(static::$set) {
      echo "Warning: Testing Environment was already set up.\n";
      return;
    }
    Timer::start(static::TIMER_NAME);
    static::$set = true;
  }
}
?>