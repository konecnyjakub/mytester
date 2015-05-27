<?php
namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 */
abstract class Environment {
  /** @var int */
  static $taskCount = 0;
  /** @var bool */
  static protected $set = false;
  /** @var string */
  static $output;
  
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
    $output .= ". $text\n";
    echo $output;
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
      $outputs = array("screen", "file");
      if(in_array($output, $outputs)) {
        self::$output = $output;
      } else {
        echo "Warrrning: Entered invalid output. Expecting screen.\n";
        self::$output = "screen";
      }
      self::$set = true;
    } else {
      echo "Warrning: Testing Environment was already set up.\n";
    }
  }
}
