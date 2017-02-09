<?php
declare(strict_types=1);

require_once __DIR__ . "/functions.php";

require findVendorDirectory() . "/autoload.php";

$tester = new MyTester\Tester(getTestsDirectory());
$tester->execute();
?>