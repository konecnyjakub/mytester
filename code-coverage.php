<?php

declare(strict_types=1);

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Driver\Selector;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\Report\Clover;

require __DIR__ . "/vendor/autoload.php";

$filter = new Filter();
$filter->includeDirectory(__DIR__ . "/src");

$coverage = new CodeCoverage((new Selector())->forLineCoverage($filter), $filter);
$coverage->start("My Tester");
register_shutdown_function(function () use ($coverage) {
    $coverage->stop();
    (new Clover())->process($coverage, __DIR__ . "/coverage.xml");
});

$_SERVER["argv"][] = "--colors";
$_SERVER["argc"]++;
require __DIR__ . "/src/mytester.php";
