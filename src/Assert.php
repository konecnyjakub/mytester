<?php
namespace MyTester;

/**
 * Assertions
 *
 * @author Jakub Konečný
 */
class Assert {
  private function __construct() { }
  static protected function tryAssertion($code) {
    Environment::incCounter();
    if(assert($code)) {
      echo "Test " . Environment::$taskCount . " passed. Assertion $code is true.\n";
    }
  }
  
  static function same($expected, $actual) {
    self::tryAssertion("$expected == $actual");
  }
  
  static function notSame($expected, $actual) {
    self::tryAssertion("$expected !== $actual");
  }
  
  static function true($actual) {
    self::tryAssertion($actual);
  }
  static function false($actual) {
    self::tryAssertion(!$actual);
  }
  
  static function null($actual) {
    self::tryAssertion($actual == NULL);
  }
  
  static function notNull($actual) {
    self::tryAssertion($actual !== NULL);
  }
  
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
  
  static function type($type, $value) { }
}
