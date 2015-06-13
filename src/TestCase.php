<?php
namespace MyTester;

/**
 * One test suit
 *
 * @author Jakub Konečný
 * @copyright (c) 2015, Jakub Konečný
 * @license https://spdx.org/licenses/BSD-3-Clause.html BSD-3-Clause
 */
abstract class TestCase {
  const METHOD_PATTERN = '#^test[A-Z0-9_]#';
  
  /**
   * Runs the test suit
   * 
   * @return void
   */
  function run() {
    $className = get_class($this);
    $runner = new Runner($className);
    $r = new \ReflectionObject($this);
    $methods = array_values(preg_grep(self::METHOD_PATTERN, array_map(function(\ReflectionMethod $rm) {
      return $rm->getName();
    }, $r->getMethods())));
    foreach($methods as $method) {
      $parameters = false;
      $params = $r->getMethod($method)->getParameters();
      if(count($params) > 0) {
        foreach($params as $param) {
          $paramName = $param->getName();
          global $$paramName;
          $parameters[] = $$paramName;
        }
      }
      if(is_array($parameters)) $runner->addJob("$className::$method", array($this, $method), $parameters);
      else $runner->addJob("$className::$method", array($this, $method));
    }
    $output = $runner->run();
    if(Environment::$output == "screen") {
      echo $output;
    } else {
      $time = date("o-m-d-h-i-s");
      $filename = "../$className-$time.log";
      Environment::printLine("Trying to create file $filename ...");
      if(file_put_contents($filename, $output)) {
        echo "Successfuly created.";
      } else {
        echo "An error occurred.";
      }
    }
  }
}
?>