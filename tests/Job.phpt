<?php
namespace MyTester;

class JobTest {
  function test() {
    Environment::testResult("Test");
  }
  
  function testParams(array $params) {
    Assert::same("abc", $params[0]);
  }
}

$test = new JobTest();
$job = new Job("Test Job", array($test, "test"));
$params = array("abc");
$job2 = new Job("Test Job with Params", array($test, "testParams"), $params);

echo $job->execute();
echo $job2->execute();
?>