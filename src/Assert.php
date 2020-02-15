<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Assertions
 *
 * @author Jakub Konečný
 */
final class Assert {
  use \Nette\StaticClass;

  /**
   * @param string|array $variable
   */
  private static function showStringOrArray($variable): string {
    return (is_string($variable) ? $variable : "(array)");
  }
  
  /**
   * Tries an assertion
   * 
   * @param mixed $code Assertion to try
   * @param string $failureText Text to print on failure
   */
  public static function tryAssertion($code, string $failureText = ""): void {
    $success = ($code == true);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = ($failureText === "") ? "Assertion \"$code\" is not true." : $failureText;
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Are both values same?
   * 
   * @param mixed $expected
   * @param mixed $actual
   */
  public static function same($expected, $actual): void {
    $success = ($expected == $actual);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = "The value is not $expected but $actual.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Are not both values same?
   * 
   * @param mixed $expected
   * @param mixed $actual
   */
  public static function notSame($expected, $actual): void {
    $success = ($expected !== $actual);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = "The value is $expected.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is the expression true?
   * 
   * @param mixed $actual
   */
  public static function true($actual): void {
    $success = ($actual == true);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = "The expression is not true.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is the expression false?
   * 
   * @param mixed $actual
   */
  public static function false($actual): void {
    $success = ($actual == false);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = "The expression is not false.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is the value null?
   * 
   * @param mixed $actual
   */
  public static function null($actual): void {
    $success = ($actual == null);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = "The value is not null.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is not the value null?
   * 
   * @param mixed $actual
   */
  public static function notNull($actual): void {
    $success = ($actual !== null);
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    if(!$success) {
      $message = "The value is null.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Does $actual contain $needle?
   * 
   * @param string|array $needle
   * @param string|array $actual
   */
  public static function contains($needle, $actual): void {
    if(!is_string($needle) && !is_array($needle)) {
      Environment::testResult("The variable is not string or array.", false);
    } elseif(is_string($actual) && is_string($needle)) {
      if($needle !== "" && strpos($actual, $needle) !== false) {
        Environment::testResult("");
      } else {
        Environment::testResult("$needle is not in the variable.", false);
      }
    } elseif(is_array($actual)) {
      if(in_array($needle, $actual)) {
        Environment::testResult(self::showStringOrArray($needle) . " is in the variable.");
      } else {
        Environment::testResult(self::showStringOrArray($needle) . " is not in the variable.", false);
      }
    } else {
      Environment::testResult(self::showStringOrArray($needle) . " is not in the variable.", false);
    }
  }
  
  /**
   * Does $actual not contain $needle?
   * 
   * @param string|array $needle
   * @param string|array $actual
   */
  public static function notContains($needle, $actual): void {
    if(!is_string($needle) && !is_array($needle)) {
      Environment::testResult("The variable is not string or array.", false);
    } elseif(is_string($actual) && is_string($needle)) {
      if($needle === "" || strpos($actual, $needle) === false) {
        Environment::testResult("");
      } else {
        Environment::testResult("$needle is in the variable.", false);
      }
    } elseif(is_array($actual)) {
      if(!in_array($needle, $actual)) {
        Environment::testResult("");
      } else {
        Environment::testResult(self::showStringOrArray($needle) . " is in the variable.", false);
      }
    } else {
      Environment::testResult(self::showStringOrArray($needle) . " is not in the variable.", false);
    }
  }
  
  /**
   * Does $value contain $count items?
   *
   * @param string|array|\Countable $value
   */
  public static function count(int $count, $value): void {
    if(!is_array($value) && !$value instanceof \Countable) {
      Environment::testResult("The variable is not array or countable object.", false);
    } elseif(count($value) === $count) {
      Environment::testResult("");
    } else {
      $actual = count($value);
      Environment::testResult("Count of the variable is $actual.", false);
    }
  }
  
  /**
   * Does $value not contain $count items?
   *
   * @param string|array|\Countable $value
   */
  public static function notCount(int $count, $value): void {
    if(!is_array($value) && !$value instanceof \Countable) {
      Environment::testResult("The variable is not array or countable object.", false);
    } elseif(count($value) === $count) {
      $actual = count($value);
      Environment::testResult("Count of the variable is $actual.", false);
    } else {
      Environment::testResult("");
    }
  }
  
  /**
   * Is $value of type $type?
   * 
   * @param string|object $type
   * @param mixed $value
   */
  public static function type($type, $value): void {
    if(!is_object($type) && !is_string($type)) {
      Environment::testResult("Type must be string or object.", false);
    } elseif(in_array($type, ["array", "bool", "callable", "float",
      "int", "integer", "null", "object", "resource", "scalar", "string"], true)) {
      if(!call_user_func("is_$type", $value)) {
        Environment::testResult("The variable is " . gettype($value) . ".", false);
      } else {
        Environment::testResult("");
      }
    } elseif(!$value instanceof $type) {
      $actual = is_object($value) ? get_class($value) : gettype($value);
      Environment::testResult("The variable is instance of $actual.", false);
    } else {
      Environment::testResult("");
    }
  }
}
?>