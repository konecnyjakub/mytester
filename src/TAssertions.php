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

  protected function isSuccess(bool $success): bool {
    if(Environment::getShouldFail()) {
      $success = !$success;
    }
    return $success;
  }
  
  /**
   * Tries an assertion
   *
   * @param mixed $code Assertion to try
   */
  protected function assert($code, string $failureText = ""): void {
    $success = $this->isSuccess($code == true);
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
    $success = $this->isSuccess($expected == $actual);
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
    $success = $this->isSuccess($expected !== $actual);
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
    $success = $this->isSuccess($actual == true);
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
    $success = $this->isSuccess($actual == false);
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
    $success = $this->isSuccess($actual == null);
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
    $success = $this->isSuccess($actual !== null);
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
      $success = $this->isSuccess($needle !== "" && strpos($actual, $needle) !== false);
      if($success) {
        Environment::testResult("");
      } else {
        Environment::testResult("$needle is not in the variable.", false);
      }
    } elseif(is_array($actual)) {
      $success = $this->isSuccess(in_array($needle, $actual));
      if($success) {
        Environment::testResult("");
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
      $success = $this->isSuccess($needle === "" || strpos($actual, $needle) === false);
      if($success) {
        Environment::testResult("");
      } else {
        Environment::testResult("$needle is in the variable.", false);
      }
    } elseif(is_array($actual)) {
      $success = $this->isSuccess(!in_array($needle, $actual));
      if($success) {
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
      return;
    }
    $success = $this->isSuccess(count($value) === $count);
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
   */
  protected function assertNotCount(int $count, $value): void {
    if(!is_array($value) && !$value instanceof \Countable) {
      Environment::testResult("The variable is not array or countable object.", false);
      return;
    }
    $success = $this->isSuccess(count($value) !== $count);
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
   */
  protected function assertType($type, $value): void {
    if(!is_object($type) && !is_string($type)) {
      Environment::testResult("Type must be string or object.", false);
      return;
    }
    if(in_array($type, ["array", "bool", "float", "int", "string", "null", "object", "resource",
      "scalar", "iterable", "callable", ], true)) {
      $success = $this->isSuccess(call_user_func("is_$type", $value));
      if(!$success) {
        Environment::testResult("The variable is " . gettype($value) . ".", false);
      } else {
        Environment::testResult("");
      }
      return;
    }
    $success = $this->isSuccess($value instanceof $type);
    if(!$success) {
      $actual = get_debug_type($value);
      Environment::testResult("The variable is instance of $actual.", false);
    } else {
      Environment::testResult("");
    }
  }
}
?>