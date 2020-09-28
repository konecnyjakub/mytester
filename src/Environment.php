<?php
declare(strict_types=1);

namespace MyTester;

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
  public const VERSION = "2.1.0-dev";

  private static int $taskCount = 0;
  private static bool $set = false;
  /** @var SkippedTest[] */
  private static array $skipped = [];
  public static string $currentJob = "";
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
  
  public static function getShouldFail(): bool {
    return static::$shouldFail;
  }
  
  public static function setShouldFail(bool $value): void {
    static::$shouldFail = $value;
  }
  
  /**
   * Print version of My Tester and PHP
   */
  public static function printInfo(): void {
    echo static::NAME . " " . static::VERSION . "\n";
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
    $time = \Tracy\Debugger::timer(static::NAME);
    $resultsLine .= ", $time second(s))";
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
    \Tracy\Debugger::timer(static::NAME);
    static::$set = true;
  }
}
?>