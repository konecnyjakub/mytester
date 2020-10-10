<?php

declare(strict_types=1);

require_once __DIR__ . "/functions.php";

require findVendorDirectory() . "/autoload.php";

use MyTester\Tester;

$tester = new Tester(getTestsDirectory());
$tester->execute();
