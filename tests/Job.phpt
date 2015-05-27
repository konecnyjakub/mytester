<?php
namespace MyTester;

class JobTest {
  function test() {
    Environment::testResult("Test");
  }
}

$test = new JobTest();
$job = new Job("Test Job", array($test, "test"));

echo $job->execute();
?>