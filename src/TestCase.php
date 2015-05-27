<?php
namespace MyTester;

/**
 * One test suit
 *
 * @author Jakub Konečný
 */
abstract class TestCase {
  const METHOD_PATTERN = '#^test[A-Z0-9_]#';
  
  function run() {
    $className = get_class($this);
    $runner = new Runner($className);
    $r = new \ReflectionObject($this);
    $methods = array_values(preg_grep(self::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $runner->addJob($className . "::$method", array($this, $method));
    }
    $output = $runner->run();
    foreach($output as $line) {
      echo "$line";
    }
  }
}
