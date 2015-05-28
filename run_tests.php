<?php
require "./src/bootstrap.php";
MyTester\Environment::setup();

foreach(glob("./tests/*.phpt") as $filename) {
  require $filename;
}
?>