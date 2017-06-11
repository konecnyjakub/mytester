<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Assertions
 *
 * @author Jakub Konečný
 * @copyright (c) 2015-2017, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class Assert {
  use \Nette\StaticClass;
  
  /**
   * Tries an assertion
   * 
   * @param mixed $code Assertion to try
   * @param string $successText Text to print on success
   * @param string $failureText Text to print on failure
   * @return void
   */
  static function tryAssertion($code, string $successText = "", string $failureText = ""): void {
    $success = assert($code);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if($success) {
      $message = ($successText === "") ? "Assertion \"$code\" is true." : $successText;
    } else {
      $message = ($failureText === "") ? "Assertion \"$code\" is not true." : $failureText;
    }
    Environment::testResult($message, $success);
  }
  
  /**
   * Are both values same?
   * 
   * @param mixed $expected
   * @param mixed $actual
   * @return void
   */
  static function same($expected, $actual): void {
    $success = ($expected == $actual);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if($success) {
      $message = "The value is $expected.";
    } else {
      $message = "The value is not $expected but $actual.";
    }
    Environment::testResult($message, $success);
  }
  
  /**
   * Are not both values same?
   * 
   * @param mixed $expected
   * @param mixed $actual
   * @return void
   */
  static function notSame($expected, $actual): void {
    static::tryAssertion("$expected !== $actual", "The value is not $expected.", "The value is $expected.");
  }
  
  /**
   * Is the expression true?
   * 
   * @param mixed $actual
   * @return void
   */
  static function true($actual): void {
    static::tryAssertion($actual, "The expression is true.", "The expression is not true.");
  }
  
  /**
   * Is the expression false?
   * 
   * @param mixed $actual
   * @return void
   */
  static function false($actual): void {
    static::tryAssertion(!$actual, "The expression is false.", "The expression is not false.");
  }
  
  /**
   * Is the value null?
   * 
   * @param mixed $actual
   * @return void
   */
  static function null($actual): void {
    static::tryAssertion($actual == NULL, "The value is null.", "The value is not null.");
  }
  
  /**
   * Is not the value null?
   * 
   * @param mixed $actual
   * @return void
   */
  static function notNull($actual): void {
    static::tryAssertion($actual !== NULL, "The value is not null.", "The value is null.");
  }
  
  /**
   * Does $actual contain $needle?
   * 
   * @param string|array $needle
   * @param string|array $actual
   * @return void
   */
  static function contains($needle, $actual): void {
    if(!is_string($needle) AND !is_array($needle)) {
      Environment::testResult("The variable is not string or array.", false);
    } elseif(is_string($actual)) {
      if($needle !== "" AND strpos($actual, $needle) !== FALSE) {
        Environment::testResult("$needle is in the variable.");
      } else {
        Environment::testResult("$needle is not in the variable.", false);
      }
    } elseif(is_array($actual)) {
      if(in_array($needle, $actual)) {
        Environment::testResult("$needle is in the variable.");
      } else {
        Environment::testResult("$needle is not in the variable.", false);
      }
    } else {
      Environment::testResult("$needle is not in the variable.", false);
    }
  }
  
  /**
   * Does $actual not contain $needle?
   * 
   * @param string|array $needle
   * @param string|array $actual
   * @return void
   */
  static function notContains($needle, $actual): void {
    if(!is_string($needle) AND !is_array($needle)) {
      Environment::testResult("The variable is not string or array.", false);
    } elseif(is_string($actual)) {
      if($needle === "" OR strpos($actual, $needle) === FALSE) {
        Environment::testResult("$needle is not in the variable.");
      } else {
        Environment::testResult("$needle is in the variable.", false);
      }
    } elseif(is_array($actual)) {
      if(!in_array($needle, $actual)) {
        Environment::testResult("$needle is not in the variable.");
      } else {
        Environment::testResult("$needle is in the variable.", false);
      }
    } else {
      Environment::testResult("$needle is not in the variable.", false);
    }
  }
  
  /**
   * Does $value contain $count items?
   * 
   * @param int $count
   * @param string|array|\Countable $value
   * @return void
   */
  static function count(int $count, $value): void {
    if(!is_array($value) AND !$value instanceof \Countable) {
      Environment::testResult("The variable is not array or countable object.", false);
    } elseif(count($value) == $count) {
      Environment::testResult("Count of the variable is $count.");
    } else {
      $actual = count($value);
      Environment::testResult("Count of the variable is $actual.", false);
    }
  }
  
  /**
   * Does $value not contain $count items?
   * 
   * @param int $count
   * @param string|array|\Countable $value
   * @return void
   */
  static function notCount(int $count, $value): void {
    if(!is_array($value) AND !$value instanceof \Countable) {
      Environment::testResult("The variable is not array or countable object.", false);
    } elseif(count($value) == $count) {
      $actual = count($value);
      Environment::testResult("Count of the variable is $actual.", false);
    } else {
      Environment::testResult("Count of the variable is is not $count.");
    }
  }
  
  /**
   * Is $value of type $type?
   * 
   * @param string $type
   * @param mixed $value
   * @return void
   */
  static function type($type, $value): void {
    if(!is_object($type) AND !is_string($type)) {
      Environment::testResult("Type must be string or object.", false);
    } elseif(in_array($type, ["array", "bool", "callable", "float",
      "int", "integer", "null", "object", "resource", "scalar", "string"], true)) {
      if(!call_user_func("is_$type", $value)) {
        Environment::testResult("The variable is " . gettype($value) . ".", false);
      } else {
        Environment::testResult("The variable is $type.");
      }
    } elseif (!$value instanceof $type) {
      $actual = is_object($value) ? get_class($value) : gettype($value);
      Environment::testResult("The variable is instance of $actual.", false);
    } else {
      Environment::testResult("The variable is instance of $type.");
    }
  }
}
?>