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
  public const VERSION = "2.0.0-dev";

  public const MODE_CLI = "cli";
  public const MODE_HTTP = "http";

  private static int $taskCount = 0;
  private static bool $set = false;
  private static string $mode;
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
   */
  public static function incCounter(): void {
    static::$taskCount++;
  }
  
  /**
   * Resets task counter
   */
  public static function resetCounter(): void {
    static::$taskCount = 0;
  }
  
  public static function getCounter(): int {
    return static::$taskCount;
  }
  
  public static function getMode(): string {
    return static::$mode;
  }
  
  /**
   * Prints entered text with correct line ending
   */
  public static function printLine(string $text): void {
    if(static::$mode === static::MODE_HTTP) {
      $text .= "<br>";
    }
    echo "$text\n";
  }

  public static function addResult(string $result): void {
    static::$results .= $result;
  }

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
    static::printLine("");
    static::printLine("PHP " . PHP_VERSION . "(" . PHP_SAPI . ")");
    static::printLine("");
  }

  public static function printResults(): void {
    $results = static::$results;
    static::printLine($results);
    static::printSkipped();
    static::printFailed();
    static::printLine("");
    $failed = str_contains($results, TestCase::RESULT_FAILED);
    if(!$failed) {
      echo "OK";
    } else {
      echo "Failed";
    }
    $resultsLine = " (" . strlen($results) . " tests";
    if($failed) {
      $resultsLine .= ", " . substr_count($results, TestCase::RESULT_FAILED) . " failed";
    }
    if(str_contains($results, TestCase::RESULT_SKIPPED)) {
      $resultsLine .= ", " . substr_count($results, TestCase::RESULT_SKIPPED) . " skipped";
    }
    $resultsLine .= ")";
    static::printLine($resultsLine);
  }

  /**
   * Print info about skipped tests
   * @internal
   */
  public static function printSkipped(): void {
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
   * @internal
   */
  public static function printFailed(): void {
    $files = Finder::findFiles("*.errors")->in(\getTestsDirectory());
    /** @var \SplFileInfo $file */
    foreach($files as $name => $file) {
      static::printLine("--- " . substr($file->getBasename(), 0, -7));
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
    register_shutdown_function(function(): void {
      $time = \Tracy\Debugger::timer(static::NAME);
      static::printLine("");
      static::printLine("Total run time: $time second(s)");
    });
    \Tracy\Debugger::timer(static::NAME);
    static::$mode = ((PHP_SAPI === "cli") ? static::MODE_CLI : static::MODE_HTTP);
    static::$set = true;
  }
}
?>