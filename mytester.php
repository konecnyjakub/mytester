#!/usr/bin/env php
<?php
require __DIR__ . "/vendor/autoload.php";

$tester = new MyTester\Tester(__DIR__ . "/tests");
$tester->execute();
?>