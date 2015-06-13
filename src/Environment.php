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
  static $taskCount = 0;
  /** @var bool */
  static protected $set = false;
  /** @var string */
  static $output;
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
  static function getMode() {
    return self::$mode;
  }
  
  /**
   * Prints entered text with correct line ending
   * 
   * @param string $text Text to print
   */
  static function printLine($text) {
    if(self::$mode == "http" AND self::$output == "screen") echo "$text<br>\n";
    else echo "$text\n";
  }
  
  /**
   * Called when an assertion fails, prints details about the failure
   * 
   * @deprecated
   * 
   * @param string $file File where assertion failed
   * @param string $line Line where assertion failed
   * @param string $code Assertion
   */
  static function assertionFail($file, $line, $code) {
    self::testResult("Assertion \"$code\" is not true.", false);
  }
  
  /**
   * Sets up the environment
   * 
   * @param string $output Where print results   
   * @return void
   */
  static function setup($output = "screen") {
    if(!self::$set) {
      assert_options(ASSERT_QUIET_EVAL, 1);
      assert_options(ASSERT_WARNING, 0);
      //assert_options(ASSERT_CALLBACK, array(__CLASS__, "assertionFail"));
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