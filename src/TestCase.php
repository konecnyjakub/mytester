<?php
namespace MyTester;

/**
 * Description of TestCase
 *
 * @author Jakub Konečný
 */
abstract class TestCase {
  const METHOD_PATTERN = '#^test[A-Z0-9_]#';
  private $runner;
  function run() {
    $className = get_class($this);
    $this->runner = new Runner($className);
    $r = new \ReflectionObject($this);
    $methods = array_values(preg_grep(self::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $this->runner->addJob($className . "::$method", array($this, $method));
    }
    $output = $this->runner->run();
    foreach($output as $line) {
      echo "$line";
    }
  }
}
