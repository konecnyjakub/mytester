<?php
namespace MyTester;
define("TESTS_DIR", dirname(__FILE__) . "..");

require_once "../src/bootstrap.php";
Environment::setup();

foreach(glob("../tests/*.phpt") as $filename) {
  require TESTS_DIR . "/$filename";
}
?>