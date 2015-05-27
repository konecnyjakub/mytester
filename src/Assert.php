<?php
namespace MyTester;

/**
 * Assertions
 *
 * @author Jakub Konečný
 */
abstract class Assert {
  
  /**
   * Tries an assertion
   * 
   * @param string $code Assertion to try
   * @return void
   */
  static function tryAssertion($code) {
    if(assert($code)) {
      Environment::testResult("Assertion $code is true.");
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
    if(!is_array($actual)) {
      Environment::testResult("\$actual is not array.", false);
      return;
    }
    if(!is_string($needle) AND !is_array($needle)) {
      Environment::testResult("\$needle is not string or array.", false);
      return;
    }
    if(in_array($needle, $actual)) {
      Environment::testResult("$needle is in \$actual.");
    } else {
      Environment::testResult("$needle is not in \$actual.", false);
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
    if(!is_array($actual)) {
      Environment::testResult("\$actual is not array.", false);
      return;
    }
    if(!is_string($needle) AND !is_array($needle)) {
      Environment::testResult("\$needle is not string or array.", false);
      return;
    }
    if(!in_array($needle, $actual)) {
      Environment::testResult("$needle is not in \$actual.");
    } else {
      Environment::testResult("$needle is in \$actual.", false);
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
    if(!is_array($value) AND !$value instanceof \Countable) {
      Environment::testResult("\$value is not array or countable object.", false);
      return;
    }
    if(count($value) == $count) {
      Environment::testResult("Count of \$value is $count.");
    } else {
      Environment::testResult("Count of \$value is not $count.", false);
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
