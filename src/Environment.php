<?php
namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 * @copyright (c) 2015, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class Environment {
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
    self::incCounter();
    $output = "Test " . self::$taskCount . " ";
    if($success) $output .= "passed";
    else $output .= "failed";
    self::printLine($output . ". $text");
  }
  
  /**
   * Print stats for a test
   * 
   * @param string $results
   * @param int $time_start
   * @param int $time_end
   * @return void
   */
  static function testStats($results, $time_start, $time_end) {
    $testsPassed = substr_count($results, " passed. ");
    $testsFailed = substr_count($results, " failed. ");
    $testsTotal = $testsPassed + $testsFailed;
    self::printLine("Executed $testsTotal tests. $testsPassed passed, $testsFailed failed.");
    $time = $time_end - $time_start;
    self::printLine("Execution time: $time second(s)");
  }
  
  /**
   * Checks if environment was set
   * 
   * @return bool
   */
  static function isSetUp() {
    return self::$set;
  }
  
  /**
   * Increases task counter
   * 
   * @return void
   */
  static function incCounter() {
    self::$taskCount++;
  }
  
  /**
   * Resets task counter
   * 
   * @return void
   */
  static function resetCounter() {
    self::$taskCount = 0;
  }
  
  /**
   * @return string
   */
  static function getCounter() {
    return self::$taskCount;
  }
  
  /**
   * @return string
   */
  static function getOutput() {
    return self::$output;
  }
  
  /**
   * @return string
   */     
  static function getMode() {
    return self::$mode;
  }
  
  /**
   * Prints entered text with correct line ending
   * 
   * @param string $text Text to print
   * @param bool $ignoreOutput Whetever to ignore output, useful only in http mode
   */
  static function printLine($text, $ignoreOutput = false) {
    if(self::$mode == "http" AND $ignoreOutput) echo "$text<br>\n";
    elseif(self::$mode == "http" AND self::$output == "screen") echo "$text<br>\n";
    else echo "$text\n";
  }
  
  /**
   * Sets up the environment
   * 
   * @param string $output Where print results   
   * @return void
   */
  static function setup($output = "screen") {
    if(!self::$set) {
      assert_options(ASSERT_ACTIVE, 1);
      assert_options(ASSERT_QUIET_EVAL, 1);
      assert_options(ASSERT_WARNING, 0);
      self::$mode = (PHP_SAPI == "cli" ? "cli": "http");
      $outputs = array("screen", "file");
      if(in_array($output, $outputs)) {
        self::$output = $output;
      } else {
        self::printLine("Warrrning: Entered invalid output. Expecting screen.");
        self::$output = "screen";
      }
      self::$set = true;
    } else {
      self::printLine("Warrning: Testing Environment was already set up.");
    }
  }
}
?>