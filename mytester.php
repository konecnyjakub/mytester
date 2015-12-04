<?php
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/src/bootstrap.php";
MyTester\Environment::setup();

$tester = new MyTester\Tester(__DIR__ . "/tests");
$tester->execute();
?>