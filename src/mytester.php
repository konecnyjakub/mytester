<?php

declare(strict_types=1);

require_once __DIR__ . "/functions.php";

require findVendorDirectory() . "/autoload.php";

use MyTester\Tester;
use Nette\CommandLine\Parser;

$cmd = new Parser("", [
    "path" => [
        Parser::VALUE => dirname(findVendorDirectory()) . "/tests",
    ],
    "--colors" => [
        Parser::OPTIONAL => true,
    ],
]);
$options = $cmd->parse();

$tester = new Tester($options["path"]);
$tester->useColors = isset($options["--colors"]);
$tester->execute();
