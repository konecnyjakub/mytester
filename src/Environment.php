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
  const VERSION = "1.1-dev";
  
  /** @var int */
  static protected $taskCount = 0;
  /** @var bool */
  static protected $set = false;
  /** @var string */
  static protected $output;
  /** @var string */
  static protected $mode;
  
  /**
   * Prints result of a test
   * 
   * @param string $text Details
   * @param bool $success Whetever the test was successful
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
   * Print stats for a test
   * 
   * @param string $results
   * @param string $timer
   * @return void
   */
  static function testStats($results, $timer) {
    $testsPassed = substr_count($results, " passed. ");
    $testsFailed = substr_count($results, " failed. ");
    $testsTotal = $testsPassed + $testsFailed;
    static::printLine("Executed $testsTotal tests. $testsPassed passed, $testsFailed failed.");
    $jobsExecuted = substr_count($results, "*Finished ");
    $jobsSkipped = substr_count($results, "*Skipping ");
    if($jobsExecuted OR $jobsSkipped) static::printLine("Executed $jobsExecuted job(s), skipped $jobsSkipped.");
    $time = \Tracy\Debugger::timer($timer);
    static::printLine("Execution time: $time second(s)");
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
  static function getOutput() {
    return static::$output;
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
   * @param bool $ignoreOutput Whetever to ignore output, useful only in http mode
   */
  static function printLine($text, $ignoreOutput = false) {
    if(static::$mode == "http" AND $ignoreOutput) echo "$text<br>\n";
    elseif(static::$mode == "http" AND static::$output == "screen") echo "$text<br>\n";
    else echo "$text\n";
  }
  
  /**
   * Print version of My Tester and PHP
   * 
   * @return void
   */
  static function printInfo() {
    static::printLine(static::NAME . " " . static::VERSION, true);
    static::printLine("", true);
    static::printLine("PHP " . PHP_VERSION . "(" . PHP_SAPI . ")", true);
    static::printLine("", true);
  }
  
  /**
   * Sets up the environment
   * 
   * @param string $output Where print results   
   * @return void
   */
  static function setup($output = "screen") {
    if(!static::$set) {
      assert_options(ASSERT_ACTIVE, 1);
      assert_options(ASSERT_QUIET_EVAL, 1);
      assert_options(ASSERT_WARNING, 0);
      register_shutdown_function(function() {
        $time = \Tracy\Debugger::timer(static::NAME);
        static::printLine("Total run time: $time second(s)");
      });
      \Tracy\Debugger::timer(static::NAME);
      static::$mode = (PHP_SAPI == "cli" ? "cli": "http");
      if(in_array($output, ["screen", "file"])) {
        static::$output = $output;
      } else {
        static::printLine("Warning: Entered invalid output. Expecting screen or file.");
        static::$output = "screen";
      }
      static::$set = true;
    } else {
      static::printLine("Warning: Testing Environment was already set up.");
    }
  }
}
?>
