<?php
declare(strict_types=1);

require_once __DIR__ . "/functions.php";

$vendorDirectory = findVendorDirectory();

require $vendorDirectory . "/autoload.php";

use Composer\InstalledVersions;
use MyTester\CodeCoverage\CodeCoverageExtension;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\CodeCoverage\Formatters\PercentFormatter;
use MyTester\ICustomFileNameResultsFormatter;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
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
    "--coverageFile" => [
        Parser::Argument => true,
        Parser::Optional => true,
    ],
    "--resultsFormat" => [
        Parser::Argument => true,
        Parser::Optional => true,
        Parser::Enum => array_keys(ResultsHelper::$availableFormatters),
    ],
    "--resultsFile" => [
        Parser::Argument => true,
        Parser::Optional => true,
    ],
    "--version" => [
        Parser::Optional => true,
    ],
]);
$options = $cmd->parse();

if (isset($options["--version"])) {
    $version = InstalledVersions::getPrettyVersion("konecnyjakub/mytester");
    echo "My Tester $version\n";
    exit;
}

$codeCoverageCollector = new Collector();
foreach (CodeCoverageHelper::$defaultEngines as $engine) {
    $codeCoverageCollector->registerEngine(new $engine()); // @phpstan-ignore argument.type
}
$codeCoverageCollector->registerFormatter(new PercentFormatter());
$coverageFormat = $options["--coverageFormat"];
if ($coverageFormat !== null) {
    $type = CodeCoverageHelper::$availableFormatters[$coverageFormat];
    /** @var \MyTester\CodeCoverage\ICodeCoverageFormatter $codeCoverageFormatter */
    $codeCoverageFormatter = new $type();
    if (
        $codeCoverageFormatter instanceof \MyTester\CodeCoverage\ICodeCoverageCustomFileNameFormatter &&
        isset($options["--coverageFile"])
    ) {
        $codeCoverageFormatter->setOutputFileName($options["--coverageFile"]);
    }
    $codeCoverageCollector->registerFormatter($codeCoverageFormatter);
}

$resultsFormatter = null;
$resultsFormat = $options["--resultsFormat"];
if ($resultsFormat !== null) {
    $type = ResultsHelper::$availableFormatters[$resultsFormat];
    /** @var \MyTester\IResultsFormatter $resultsFormatter */
    $resultsFormatter = new $type();
    if ($resultsFormatter instanceof ICustomFileNameResultsFormatter && isset($options["--resultsFile"])) {
        $resultsFormatter->setOutputFileName($options["--resultsFile"]);
    }
}

$extensions = [
    new CodeCoverageExtension($codeCoverageCollector),
];

$tester = new Tester(folder: $options["path"], extensions: $extensions, resultsFormatter: $resultsFormatter);
$tester->useColors = isset($options["--colors"]);
$tester->execute();
