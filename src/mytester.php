<?php
declare(strict_types=1);

require_once __DIR__ . "/functions.php";

$vendorDirectory = findVendorDirectory();

require $vendorDirectory . "/autoload.php";

use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\CodeCoverage\PercentFormatter;
use MyTester\Tester;
use Nette\CommandLine\Parser;

$cmd = new Parser("", [
    "path" => [
        Parser::Default => $vendorDirectory . "/../tests",
    ],
    "--colors" => [
        Parser::Optional => true,
    ],
    "--coverageFormat" => [
        Parser::Argument => true,
        Parser::Optional => true,
        Parser::Enum => array_keys(CodeCoverageHelper::$availableFormatters),
    ],
]);
$options = $cmd->parse();

$codeCoverageCollector = new Collector();
foreach (CodeCoverageHelper::$defaultEngines as $engine) {
    $codeCoverageCollector->registerEngine(new $engine()); // @phpstan-ignore argument.type
}
$codeCoverageCollector->registerFormatter(new PercentFormatter());
$coverageFormat = $options["--coverageFormat"];
if ($coverageFormat !== null) {
    $type = CodeCoverageHelper::$availableFormatters[$coverageFormat];
    $codeCoverageCollector->registerFormatter(new $type()); // @phpstan-ignore argument.type
}

$tester = new Tester(folder: $options["path"], codeCoverageCollector: $codeCoverageCollector);
$tester->useColors = isset($options["--colors"]);
$tester->execute();
