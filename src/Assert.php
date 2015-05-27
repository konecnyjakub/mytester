<?php
namespace MyTester;

/**
 * Assertions
 *
 * @author Jakub Konečný
 */
class Assert {
  private function __construct() { }
  
  /**
   * Tries an assertion
   * 
   * @param string $code Assertion to try
   * @return void
   */
  static protected function tryAssertion($code) {
    Environment::incCounter();
    if(assert($code)) {
      echo "Test " . Environment::$taskCount . " passed. Assertion $code is true.\n";
    }
  }
  
  /**
   * Are both values same?
   * 
   * @param mixed $expected
   * @param mixed $actual
   * @return void
   */
  static function same($expected, $actual) {
    self::tryAssertion("$expected == $actual");
  }
  
  /**
   * Are not both values same?
   * 
   * @param mixed $expected
   * @param mixed $actual
   * @return void
   */
  static function notSame($expected, $actual) {
    self::tryAssertion("$expected !== $actual");
  }
  
  /**
   * Is the expression true?
   * 
   * @param mixed $actual
   * @return void
   */
  static function true($actual) {
    self::tryAssertion($actual);
  }
  
  /**
   * Is the expression false?
   * 
   * @param mixed $actual
   * @return void
   */
  static function false($actual) {
    self::tryAssertion(!$actual);
  }
  
  /**
   * Is the value null?
   * 
   * @param mixed $actual
   * @return void
   */
  static function null($actual) {
    self::tryAssertion($actual == NULL);
  }
  
  /**
   * Is not the value null?
   * 
   * @param mixed $actual
   * @return void
   */
  static function notNull($actual) {
    self::tryAssertion($actual !== NULL);
  }
  
  /**
   * Does $actual contain $needle?
   * 
   * @param string|array $needle
   * @param array $actual
   * @return void
   */
  static function contains($needle, $actual) {
    Environment::incCounter();
    if(!is_array($actual)) {
      echo "Test " . Environment::$taskCount . " failed. \$actual is not array\n";
      return;
    }
    if(!is_string($needle) AND !is_array($needle)) {
      echo "Test " . Environment::$taskCount . " failed. \$needle is not string or array\n";
      return;
    }
    if(in_array($needle, $actual)) {
      echo "Test " . Environment::$taskCount . " passed. $needle is in \$actual\n";
    } else {
      echo "Test " . Environment::$taskCount . " $needle is not in \$actual\n";
    }
  }
  
  /**
   * Does $actual not contain $needle?
   * 
   * @param string|array $needle
   * @param array $actual
   * @return void
   */
  static function notContains($needle, $actual) {
    Environment::incCounter();
    if(!is_array($actual)) {
      echo "Test " . Environment::$taskCount . " failed. \$actual is not array\n";
      return;
    }
    if(!is_string($needle) AND !is_array($needle)) {
      echo "Test " . Environment::$taskCount . " failed. \$needle is not string or array\n";
      return;
    }
    if(!in_array($needle, $actual)) {
      echo "Test " . Environment::$taskCount . " passed. $needle is not in \$actual\n";
    } else {
      echo "Test " . Environment::$taskCount . " failed. $needle is in \$actual\n";
    }
  }
  
  /**
   * Does $value contain $count items?
   * 
   * @param int $count
   * @param array|\Countable $value
   * @return void
   */
  static function count($count, $value) {
    Environment::incCounter();
    if(!is_array($value) AND !$value instanceof \Countable) {
      echo "Test " . Environment::$taskCount . " failed. \$value is not array or countable object\n";
      return;
    }
    if(count($value) == $count) {
      echo "Test " . Environment::$taskCount . " passed. Count of \$value is $count\n";
    } else {
      echo "Test " . Environment::$taskCount . " failed. Count of \$value is not $count\n";
    }
  }
  
  /**
   * Is $value of type $type?
   * @todo implement
   * 
   * @param string $type
   * @param mixed $value
   * @return void
   */
  static function type($type, $value) { }
}
