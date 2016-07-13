<?php
namespace MyTester\Bridges\NetteDI;

/**
 * Autoloader for test suits
 * 
 * @param string $class
 * @return void
 */
function autoload($class) {
  foreach(TestsRunner::$autoloader as $suit) {
    if($suit[0] === $class) {
      require $suit[1];
      return;
    }
  }
}
?>