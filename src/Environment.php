<?php
namespace MyTester;

/**
 * Testing Environment
 *
 * @author Jakub Konečný
 */
class Environment {
  private function __construct() { }
  
  /**
   * Sets up the environment
   * 
   * @return void
   */
  static function setup() {
    assert_options(ASSERT_QUIET_EVAL, 1);
  }
}
