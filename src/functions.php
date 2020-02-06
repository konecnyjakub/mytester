<?php
declare(strict_types=1);

function findVendorDirectory(): string {
  $recursionLimit = 10;
  $findVendor = function($dirName = "vendor/bin", $dir = __DIR__) use (&$findVendor, &$recursionLimit) {
    if(!$recursionLimit--) {
      throw new \Exception("Cannot find vendor directory.");
    }
    $found = $dir . "/$dirName";
    if(is_dir($found) || is_file($found)) {
      return dirname($found);
    }
    return $findVendor($dirName, dirname($dir));
  };
  return $findVendor();
}

function getTestsDirectory(): string {
  if(!isset($argv[1])) {
    return dirname(findVendorDirectory()) . "/tests";
  }
  return $argv[1];
}
?>