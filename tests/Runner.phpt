<?php
namespace MyTester;

class RunnerTest {
  function test() {
    Environment::testResult("Test", false);
  }
}

$test = new RunnerTest();
$runner = new Runner("Test Runner");
$runner->addJob("Test Job", array($test, "test"));

echo $runner->run();
?>