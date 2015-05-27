<?php
namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 */
class Environment {
  static $taskCount = 0;
  static private $set = false;
  private function __construct() { }
  
  static function isSetUp() {
    return self::$set;
  }
  
  static function incCounter() {
    self::$taskCount++;
  }
  
  static function resetCounter() {
    self::$taskCount = 0;
  }
  
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
