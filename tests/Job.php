<?php
namespace MyTester;

function testJob() {
  echo "Test";
}

$job = new Job("Test Job", "testJob");
$result = $job->execute();

foreach($result as $line) {
  echo "$line";
}
?>