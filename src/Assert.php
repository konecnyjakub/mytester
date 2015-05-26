<?php
namespace MyTester;

/**
 * Description of Assert
 *
 * @author Jakub Konečný
 */
class Assert {
  private function __construct() { }
  
  static function same($expected, $actual) {
    if(assert("$expected == $actual")) {
      echo "Assertion succeeded: $expected == $actual\n";
    } else {
      echo "Assertion failed: $expected !== $actual\n";
    }
  }
  
  static function notSame($expected, $actual) {
    if(assert("$expected !== $actual")) {
      echo "Assertion succeeded: $expected !== $actual\n";
    } else {
      echo "Assertion failed: $expected == $actual\n";
    }
  }
  
  static function true($actual) {
    if(assert($actual)) {
      echo "Assertion succeeded: $actual is true\n";
    } else {
      echo "Assertion failed: $actual is false\n";
    }
  }
  static function false($actual) {
    if(assert(!$actual)) {
      echo "Assertion succeeded: $actual is false\n";
    } else {
      echo "Assertion failed: $actual is true\n";
    }
  }
  
  static function null($actual) {
    if($actual == NULL) {
      echo "Assertion succeeded: $actual is null\n";
    } else {
      echo "Assertion failed: $actual is not null\n";
    }
  }
  
  static function notNull($actual) {
    if($actual !== NULL) {
      echo "Assertion succeeded: $actual is not null\n";
    } else {
      echo "Assertion failed: $actual is null\n";
    }
  }
  
  static function contains($needle, $actual) {
    if(!is_array($actual)) {
      echo "Assertion failed: \$actual is not array\n";
      return;
    }
    if(!is_string($needle) AND !is_array($needle)) {
      echo "Assertion failed: \$needle is not string or array\n";
      return;
    }
    if(in_array($needle, $actual)) {
      echo "Assertion succeeded: $needle is in \$actual\n";
    } else {
      echo "Assertion failed: $needle is not in \$actual\n";
    }
  }
  
  static function notContains($needle, $actual) {
    if(!is_array($actual)) {
      echo "Assertion failed: \$actual is not array\n";
      return;
    }
    if(!is_string($needle) AND !is_array($needle)) {
      echo "Assertion failed: \$needle is not string or array\n";
      return;
    }
    if(!in_array($needle, $actual)) {
      echo "Assertion succeeded: $needle is not in \$actual\n";
    } else {
      echo "Assertion failed: $needle is in \$actual\n";
    }
  }
  
  static function type($type, $value) { }
}
