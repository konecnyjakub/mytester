<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Assertions
 *
 * @author Jakub Konečný
 * @deprecated Create test cases
 */
final class Assert {
  use \Nette\StaticClass;

  /**
   * @param string|array $variable
   */
  private static function showStringOrArray($variable): string {
    return (is_string($variable) ? $variable : "(array)");
  }

  private static function isSuccess(bool $success): bool {
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    return $success;
  }
  
  /**
   * Tries an assertion
   * 
   * @param mixed $code Assertion to try
   * @param string $failureText Text to print on failure
   * @deprecated
   * @see TAssertions::assert()
   */
  public static function tryAssertion($code, string $failureText = ""): void {
    $success = static::isSuccess($code == true);
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
   * @deprecated
   * @see TAssertions::assertSame()
   */
  public static function same($expected, $actual): void {
    $success = static::isSuccess($expected == $actual);
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
   * @deprecated
   * @see TAssertions::assertNotSame()
   */
  public static function notSame($expected, $actual): void {
    $success = static::isSuccess($expected !== $actual);
    if(!$success) {
      $message = "The value is $expected.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is the expression true?
   * 
   * @param mixed $actual
   * @deprecated
   * @see TAssertions::assertTruthy()
   */
  public static function true($actual): void {
    $success = static::isSuccess($actual == true);
    if(!$success) {
      $message = "The expression is not true.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is the expression false?
   * 
   * @param mixed $actual
   * @deprecated
   * @see TAssertions::assertFalsey()
   */
  public static function false($actual): void {
    $success = static::isSuccess($actual == false);
    if(!$success) {
      $message = "The expression is not false.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is the value null?
   * 
   * @param mixed $actual
   * @deprecated
   * @see TAssertions::assertNull()
   */
  public static function null($actual): void {
    $success = static::isSuccess($actual == null);
    if(!$success) {
      $message = "The value is not null.";
    }
    Environment::testResult($message ?? "", $success);
  }
  
  /**
   * Is not the value null?
   * 
   * @param mixed $actual
   * @deprecated
   * @see TAssertions::assertNotNull()
   */
  public static function notNull($actual): void {
    $success = static::isSuccess($actual !== null);
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
   * @deprecated
   * @see TAssertions::assertContains()
   */
  public static function contains($needle, $actual): void {
    if(!is_string($needle) && !is_array($needle)) {
      Environment::testResult("The variable is not string or array.", false);
    } elseif(is_string($actual) && is_string($needle)) {
      $success = static::isSuccess($needle !== "" && strpos($actual, $needle) !== false);
      if($success) {
        Environment::testResult("");
      } else {
        Environment::testResult("$needle is not in the variable.", false);
      }
    } elseif(is_array($actual)) {
      $success = static::isSuccess(in_array($needle, $actual));
      if($success) {
        Environment::testResult("");
      } else {
        Environment::testResult(static::showStringOrArray($needle) . " is not in the variable.", false);
      }
    } else {
      Environment::testResult(static::showStringOrArray($needle) . " is not in the variable.", false);
    }
  }
  
  /**
   * Does $actual not contain $needle?
   * 
   * @param string|array $needle
   * @param string|array $actual
   * @deprecated
   * @see TAssertions::assertNotContains()
   */
  public static function notContains($needle, $actual): void {
    if(!is_string($needle) && !is_array($needle)) {
      Environment::testResult("The variable is not string or array.", false);
    } elseif(is_string($actual) && is_string($needle)) {
      $success = static::isSuccess($needle === "" || strpos($actual, $needle) === false);
      if($success) {
        Environment::testResult("");
      } else {
        Environment::testResult("$needle is in the variable.", false);
      }
    } elseif(is_array($actual)) {
      $success = static::isSuccess(!in_array($needle, $actual));
      if($success) {
        Environment::testResult("");
      } else {
        Environment::testResult(static::showStringOrArray($needle) . " is in the variable.", false);
      }
    } else {
      Environment::testResult(static::showStringOrArray($needle) . " is not in the variable.", false);
    }
  }
  
  /**
   * Does $value contain $count items?
   *
   * @param string|array|\Countable $value
   * @deprecated
   * @see TAssertions::assertCount()
   */
  public static function count(int $count, $value): void {
    if(!is_array($value) && !$value instanceof \Countable) {
      Environment::testResult("The variable is not array or countable object.", false);
      return;
    }
    $success = static::isSuccess(count($value) === $count);
    if($success) {
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
   * @deprecated
   * @see TAssertions::assertNotCount()
   */
  public static function notCount(int $count, $value): void {
    if(!is_array($value) && !$value instanceof \Countable) {
      Environment::testResult("The variable is not array or countable object.", false);
      return;
    }
    $success = static::isSuccess(count($value) !== $count);
    if($success) {
      Environment::testResult("");
    } else {
      $actual = count($value);
      Environment::testResult("Count of the variable is $actual.", false);
    }
  }
  
  /**
   * Is $value of type $type?
   * 
   * @param string|object $type
   * @param mixed $value
   * @deprecated
   * @see TAssertions::assertType()
   */
  public static function type($type, $value): void {
    if(!is_object($type) && !is_string($type)) {
      Environment::testResult("Type must be string or object.", false);
      return;
    }
    if(in_array($type, ["array", "bool", "callable", "float",
      "int", "integer", "null", "object", "resource", "scalar", "string"], true)) {
      $success = static::isSuccess(call_user_func("is_$type", $value));
      if(!$success) {
        Environment::testResult("The variable is " . gettype($value) . ".", false);
      } else {
        Environment::testResult("");
      }
      return;
    }
    $success = static::isSuccess($value instanceof $type);
    if(!$success) {
      $actual = get_debug_type($value);
      Environment::testResult("The variable is instance of $actual.", false);
    } else {
      Environment::testResult("");
    }
  }
}
?>