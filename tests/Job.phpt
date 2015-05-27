<?php
namespace MyTester;

class JobTest {
  function test() {
    Environment::test("Test");
  }
}

$test = new JobTest();
$job = new Job("Test Job", array($test, "test"));
$result = $job->execute();

foreach($result as $line) {
  echo "$line";
}
?>