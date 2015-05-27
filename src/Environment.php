<?php
namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 */
class Environment {
  /** @var int */
  static $taskCount = 0;
  /** @var bool */
  static private $set = false;
  
  private function __construct() { }
  
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
   * @param string $file File where assertion failed
   * @param string $line Line where assertion failed
   * @param string $code Assertion
   */
  static function assertionFail($file, $line, $code) {
    echo "Test " . self::$taskCount . " failed. Assertion $code is not true.\n";
  }
  
  /**
   * Sets up the environment
   * 
   * @return void
   */
  static function setup() {
    if(!self::$set) {
      assert_options(ASSERT_QUIET_EVAL, 1);
      assert_options(ASSERT_WARNING, 0);
      assert_options(ASSERT_CALLBACK, array(__CLASS__, "assertionFail"));
      self::$set = true;
    } else {
      echo "Warrning: Testing Environment was already set up.\n";
    }
  }
}
