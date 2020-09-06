<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 */
final class Environment {
  use \Nette\StaticClass;
  
  public const NAME = "My Tester";
  public const VERSION = "2.0.0-dev";

  private static int $taskCount = 0;
  private static bool $set = false;
  private static string $mode;
  private static array $skipped = [];
  public static string $currentJob = "";
  public static bool $shouldFail = false;
  
  /**
   * Prints result of a test
   */
  public static function testResult(string $text, bool $success = true): void {
    static::incCounter();
    if($success) {
      return;
    }
    $output = "Test " . static::$taskCount . " failed";
    static::printLine($output . ". $text");
  }
  
  public static function checkFailed(string $results): bool {
    $testsFailed = substr_count($results, " failed. ");
    return (bool) $testsFailed;
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
    if(static::$mode === "http") {
      $text .= "<br>";
    }
    echo "$text\n";
  }
  
  /**
   * @param string|bool $reason
   */
  public static function addSkipped(string $jobName, $reason = ""): void {
    static::$skipped[] = ["name" => $jobName, "reason" => $reason];
  }
  
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

  /**
   * Print info about skipped tests
   */
  public static function printSkipped(): void {
    foreach(static::getSkipped() as $skipped) {
      $reason = "";
      if($skipped["reason"]) {
        $reason = ": {$skipped["reason"]}";
      }
      static::printLine("Skipped {$skipped["name"]}$reason");
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
    static::$mode = ((PHP_SAPI === "cli") ? "cli" : "http");
    static::$set = true;
  }
}
?>