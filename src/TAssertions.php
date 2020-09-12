<?php
declare(strict_types=1);

namespace MyTester;

trait TAssertions {
  /**
   * @param string|array $variable
   */
  protected function showStringOrArray($variable): string {
    return (is_string($variable) ? $variable : "(array)");
  }
  
  /**
   * Tries an assertion
   *
   * @param mixed $code Assertion to try
   */
  protected function assert($code, string $failureText = ""): void {
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
  protected function assertSame($expected, $actual): void {
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
  protected function assertNotSame($expected, $actual): void {
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
  protected function assertTrue($actual): void {
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
  protected function assertFalse($actual): void {
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
  protected function assertNull($actual): void {
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
  protected function assertNotNull($actual): void {
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
  protected function assertContains($needle, $actual): void {
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
        Environment::testResult($this->showStringOrArray($needle) . " is in the variable.");
      } else {
        Environment::testResult($this->showStringOrArray($needle) . " is not in the variable.", false);
      }
    } else {
      Environment::testResult($this->showStringOrArray($needle) . " is not in the variable.", false);
    }
  }

  /**
   * Does $actual not contain $needle?
   *
   * @param string|array $needle
   * @param string|array $actual
   */
  protected function assertNotContains($needle, $actual): void {
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
        Environment::testResult($this->showStringOrArray($needle) . " is in the variable.", false);
      }
    } else {
      Environment::testResult($this->showStringOrArray($needle) . " is not in the variable.", false);
    }
  }

  /**
   * Does $value contain $count items?
   *
   * @param string|array|\Countable $value
   */
  protected function assertCount(int $count, $value): void {
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
  protected function assertNotCount(int $count, $value): void {
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
  protected function assertType($type, $value): void {
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