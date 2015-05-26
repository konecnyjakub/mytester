<?php
namespace MyTester;

/**
 * Description of Environment
 *
 * @author Jakub Konečný
 */
class Environment {
  private function __construct() { }
  static function setup() {
    assert_options(ASSERT_QUIET_EVAL, 1);
  }
}
