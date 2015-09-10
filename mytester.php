<?php
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/src/bootstrap.php";

$folder = __DIR__ . "/tests";
$output = "screen";

MyTester\Environment::setup($output);

$tester = new MyTester\Tester($folder);
$tester->execute();
?>