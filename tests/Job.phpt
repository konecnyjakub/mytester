<?php
namespace MyTester;

class JobTest {
  function test() {
    Environment::testResult("Test");
  }
  
  function testParams($params, $text) {
    Assert::same("abc", $params[0]);
    Assert::same("def", $text);
  }
}

$test = new JobTest();
$job = new Job("Test Job", array($test, "test"));
$params = array(
  array("abc"), "def"
);
$job2 = new Job("Test Job with Params", array($test, "testParams"), $params);

echo $job->execute();
echo $job2->execute();
?>