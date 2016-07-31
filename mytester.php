<?php
require __DIR__ . "/vendor/autoload.php";
MyTester\Environment::setup();

$tester = new MyTester\Tester(__DIR__ . "/tests");
$tester->execute();
?>