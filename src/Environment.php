<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2017, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class Environment {
  use \Nette\StaticClass;
  
  const NAME = "My Tester";
  const VERSION = "2.0-dev";
  
  /** @var int */
  static protected $taskCount = 0;
  /** @var bool */
  static protected $set = false;
  /** @var string */
  static protected $mode;
  /** @var array */
  static protected $skipped = [];
  /** @var string */
  public static $currentJob = "";
  /** @var bool */
  public static $shouldFail = false;
  
  /**
   * Prints result of a test
   * 
   * @param string $text Details
   * @param bool $success Whether the test was successful
   * @return void
   */
  public static function testResult(string $text, bool $success = true): void {
    static::incCounter();
    if($success) {
      return;
    }
    $output = "Test " . static::$taskCount . " failed";
    static::printLine($output . ". $text");
  }
   
   /**
   * @param string $results
   * @return bool
   */
  public static function checkFailed(string $results): bool {
    $testsFailed = substr_count($results, " failed. ");
    return (bool) $testsFailed;
  }
  
  /**
   * Checks if environment was set
   * 
   * @return bool
   */
  public static function isSetUp(): bool {
    return static::$set;
  }
  
  /**
   * Increases task counter
   * 
   * @return void
   */
  public static function incCounter(): void {
    static::$taskCount++;
  }
  
  /**
   * Resets task counter
   * 
   * @return void
   */
  public static function resetCounter(): void {
    static::$taskCount = 0;
  }
  
  /**
   * @return int
   */
  public static function getCounter(): int {
    return static::$taskCount;
  }
  
  /**
   * @return string
   */     
  public static function getMode(): string {
    return static::$mode;
  }
  
  /**
   * Prints entered text with correct line ending
   * 
   * @param string $text Text to print
   * @return void
   */
  public static function printLine(string $text): void {
    if(static::$mode == "http") {
      $text .= "<br>";
    }
    echo "$text\n";
  }
  
  /**
   * @param string $jobName
   * @param string|bool $reason
   * @return void
   */
  public static function addSkipped(string $jobName, $reason = ""): void {
    static::$skipped[] = ["name" => $jobName, "reason" => $reason];
  }
  
  /**
   * @return array
   */
  public static function getSkipped(): array {
    return static::$skipped;
  }
  
  /**
   * @return bool
   */
  public static function getShouldFail(): bool {
    return static::$shouldFail;
  }
  
  /**
   * @param bool $value
   * @return void
   */
  public static function setShouldFail(bool $value): void {
    static::$shouldFail = $value;
  }
  
  /**
   * Print version of My Tester and PHP
   * 
   * @return void
   */
  public static function printInfo(): void {
    static::printLine(static::NAME . " " . static::VERSION);
    static::printLine("");
    static::printLine("PHP " . PHP_VERSION . "(" . PHP_SAPI . ")");
    static::printLine("");
  }
  
  /**
   * Sets up the environment
   *   
   * @return void
   */
  public static function setup(): void {
    if(static::$set) {
      static::printLine("Warning: Testing Environment was already set up.");
      return;
    }
    assert_options(ASSERT_ACTIVE, 1);
    assert_options(ASSERT_QUIET_EVAL, 1);
    assert_options(ASSERT_WARNING, 0);
    register_shutdown_function(function() {
      $time = \Tracy\Debugger::timer(static::NAME);
      static::printLine("");
      static::printLine("Total run time: $time second(s)");
    });
    \Tracy\Debugger::timer(static::NAME);
    static::$mode = ((PHP_SAPI == "cli") ? "cli" : "http");
    static::$set = true;
  }
}
?>