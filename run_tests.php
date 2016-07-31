<?php
require "./vendor/autoload.php";
MyTester\Environment::setup();

foreach(glob("./tests/*.phpt") as $filename) {
  require $filename;
}
?>