<?php
namespace MyTester;

require_once "./src/bootstrap.php";
Environment::setup();

foreach(glob("./tests/*.phpt") as $filename) {
  require $filename;
}
?>