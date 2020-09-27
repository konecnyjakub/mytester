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
  
  public const NAME = "My Tester";
  public const VERSION = "2.0.0";

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
    static::printLine("Test " . static::$taskCount . " failed. $text");
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
    static::printLine(static::NAME . " " . static::VERSION);
    static::printLine();
    static::printLine("PHP " . PHP_VERSION . "(" . PHP_SAPI . ")");
    static::printLine();
  }

  public static function printResults(): void {
    $results = static::$results;
    static::printLine($results);
    static::printSkipped();
    $failed = str_contains($results, TestCase::RESULT_FAILED);
    if(!$failed) {
      static::printLine();
      echo "OK";
    } else {
      static::printFailed();
      static::printLine();
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
    static::printLine($resultsLine);
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
      static::printLine("Skipped $skipped->name$reason");
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
      static::printLine("--- " . $file->getBasename($filenameSuffix));
      echo file_get_contents($name);
    }
  }
  
  /**
   * Sets up the environment
   */
  public static function setup(): void {
    if(static::$set) {
      static::printLine("Warning: Testing Environment was already set up.");
      return;
    }
    \Tracy\Debugger::timer(static::NAME);
    static::$set = true;
  }
}
?>