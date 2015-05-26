<?php
define("TESTS_DIR", dirname(__FILE__) . "..");

foreach(glob("../tests/*.php") as $filename) {
  require TESTS_DIR . "/$filename";
}
?>