<?php
namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2016, Jakub Konečný
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
  static public $currentJob = "";
  /** @var bool */
  static public $shouldFail = false;
  
  /**
   * Prints result of a test
   * 
   * @param string $text Details
   * @param bool $success Whether the test was successful
   * @return void
   */
  static function testResult($text, $success = true) {
    static::incCounter();
    $output = "Test " . static::$taskCount . " ";
    if($success) $output .= "passed";
    else $output .= "failed";
    static::printLine($output . ". $text");
  }
   
   /**
   * @param string $results
   * @return bool
   */
  static function checkFailed($results) {
    $testsFailed = substr_count($results, " failed. ");
    return (bool) $testsFailed;
  }
  
  /**
   * Checks if environment was set
   * 
   * @return bool
   */
  static function isSetUp() {
    return static::$set;
  }
  
  /**
   * Increases task counter
   * 
   * @return void
   */
  static function incCounter() {
    static::$taskCount++;
  }
  
  /**
   * Resets task counter
   * 
   * @return void
   */
  static function resetCounter() {
    static::$taskCount = 0;
  }
  
  /**
   * @return string
   */
  static function getCounter() {
    return static::$taskCount;
  }
  
  /**
   * @return string
   */     
  static function getMode() {
    return static::$mode;
  }
  
  /**
   * Prints entered text with correct line ending
   * 
   * @param string $text Text to print
   */
  static function printLine($text) {
    if(static::$mode == "http") echo "$text<br>\n";
    else echo "$text\n";
  }
  
  /**
   * @param string $jobName
   * @param string $reason
   * @return void
   */
  static function addSkipped($jobName, $reason = "") {
    static::$skipped[] = ["name" => $jobName, "reason" => $reason];
  }
  
  /**
   * @return array
   */
  static function getSkipped() { 
    return static::$skipped;
  }
  
  /**
   * @return bool
   */
  static function getShouldFail() {
    return static::$shouldFail;
  }
  
  /**
   * @param bool $value
   * @return void
   */
  static function setShouldFail($value) {
    static::$shouldFail = (bool) $value;
  }
  
  /**
   * Print version of My Tester and PHP
   * 
   * @return void
   */
  static function printInfo() {
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
  static function setup() {
    if(!static::$set) {
      assert_options(ASSERT_ACTIVE, 1);
      assert_options(ASSERT_QUIET_EVAL, 1);
      assert_options(ASSERT_WARNING, 0);
      register_shutdown_function(function() {
        $time = \Tracy\Debugger::timer(static::NAME);
        static::printLine("");
        static::printLine("Total run time: $time second(s)");
      });
      \Tracy\Debugger::timer(static::NAME);
      static::$mode = (PHP_SAPI == "cli" ? "cli": "http");
      static::$set = true;
    } else {
      static::printLine("Warning: Testing Environment was already set up.");
    }
  }
}
?>