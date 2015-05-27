<?php
namespace MyTester;

function testRunnerJob() {
  echo "Test\n";
}

$runner = new Runner("Test Runner");
$runner->addJob("Test Job", "testRunnerJob");

$output = $runner->run();
foreach($output as $line) {
  echo "$line";
}
?>