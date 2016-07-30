<?php
namespace MyTester;

/**
 * Assertions
 *
 * @author Jakub Konečný
 * @copyright (c) 2015, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class Assert extends \Nette\Object {
  /**
   * Tries an assertion
   * 
   * @param string $code Assertion to try
   * @return void
   */
  static function tryAssertion($code) {
    if(assert($code)) {
      Environment::testResult("Assertion \"$code\" is true.");
    } else {
      Environment::testResult("Assertion \"$code\" is not true.", false);
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
    $code = "$expected == $actual";
    if($expected == $actual) {
      Environment::testResult("Assertion \"$code\" is true.");
    } else {
      Environment::testResult("Assertion \"$code\" is not true.", false);
    }
  }
  
  /**
   * Are not both values same?
   * 
   * @param mixed $expected
   * @param mixed $actual
   * @return void
   */
  static function notSame($expected, $actual) {
    static::tryAssertion("$expected !== $actual");
  }
  
  /**
   * Is the expression true?
   * 
   * @param mixed $actual
   * @return void
   */
  static function true($actual) {
    static::tryAssertion($actual);
  }
  
  /**
   * Is the expression false?
   * 
   * @param mixed $actual
   * @return void
   */
  static function false($actual) {
    static::tryAssertion(!$actual);
  }
  
  /**
   * Is the value null?
   * 
   * @param mixed $actual
   * @return void
   */
  static function null($actual) {
    static::tryAssertion($actual == NULL);
  }
  
  /**
   * Is not the value null?
   * 
   * @param mixed $actual
   * @return void
   */
  static function notNull($actual) {
    static::tryAssertion($actual !== NULL);
  }
  
  /**
   * Does $actual contain $needle?
   * 
   * @param string|array $needle
   * @param array $actual
   * @return void
   */
  static function contains($needle, $actual) {
    if(!is_string($needle) AND !is_array($needle)) {
      Environment::testResult("\$needle is not string or array.", false);
    } elseif(is_string($actual)) {
      if($needle !== "" AND strpos($actual, $needle) !== FALSE) {
        Environment::testResult("$needle is in \$actual.");
      } else {
        Environment::testResult("$needle is not in \$actual.", false);
      }
    } elseif(is_array($actual)) {
      if(in_array($needle, $actual)) Environment::testResult("$needle is in \$actual.");
      else Environment::testResult("$needle is not in \$actual.", false);
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
    if(!is_string($needle) AND !is_array($needle)) {
      Environment::testResult("\$needle is not string or array.", false);
    } elseif(is_string($actual)) {
      if($needle === "" OR strpos($actual, $needle) === FALSE) {
        Environment::testResult("$needle is not in \$actual.");
      } else {
        Environment::testResult("$needle is in \$actual.", false);
      }
    } elseif(is_array($actual)) {
      if(!in_array($needle, $actual)) Environment::testResult("$needle is not in \$actual.");
      else Environment::testResult("$needle is in \$actual.", false);
    } else {
      Environment::testResult("$needle is not in \$actual.", false);
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
    } elseif(count($value) == $count) {
      Environment::testResult("Count of \$value is $count.");
    } else {
      Environment::testResult("Count of \$value is not $count.", false);
    }
  }
  
  /**
   * Is $value of type $type?
   * 
   * @param string $type
   * @param mixed $value
   * @return void
   */
  static function type($type, $value) {
    if(!is_object($type) AND !is_string($type)) {
      Environment::testResult("Type must be string or object.", false);
    } elseif(in_array($type, array("array", "bool", "callable", "float",
      "int", "integer", "null", "object", "resource", "scalar", "string"), true)) {
      if(!call_user_func("is_$type", $value)) {
        Environment::testResult(gettype($value) . " should be $type.", false);
      } else {
        Environment::testResult("\$value is $type.");
      }
    } elseif (!$value instanceof $type) {
      $actual = is_object($value) ? get_class($value) : gettype($value);
      Environment::testResult("$actual should be instance of $type.", false);
    } else {
      Environment::testResult("\$value is instance of $type.");
    }
  }
}
?>