<?php

declare(strict_types=1);

require_once __DIR__ . "/functions.php";

$vendorDirectory = findVendorDirectory();

require $vendorDirectory . "/autoload.php";

use MyTester\Tester;
use Nette\CommandLine\Parser;

$cmd = new Parser("", [
    "path" => [
        Parser::Default => $vendorDirectory . "/../tests",
    ],
    "--colors" => [
        Parser::Optional => true,
    ],
]);
$options = $cmd->parse();

$tester = new Tester($options["path"]);
$tester->useColors = isset($options["--colors"]);
$tester->execute();
