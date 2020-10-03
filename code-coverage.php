<?php
declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

$filter = new \SebastianBergmann\CodeCoverage\Filter();
$filter->includeDirectory(__DIR__ . "/src");

$coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage((new \SebastianBergmann\CodeCoverage\Driver\Selector())->forLineCoverage($filter), $filter);
$coverage->start("My Tester");
register_shutdown_function(function() use ($coverage) {
  $coverage->stop();
  (new \SebastianBergmann\CodeCoverage\Report\Clover())->process($coverage, __DIR__ . "/coverage.xml");
});

require __DIR__ . "/src/mytester.php";
?>