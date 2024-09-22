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
use MyTester\ErrorsFilesExtension;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
use MyTester\Tester;
use MyTester\TestsFolderProvider;
use Nette\CommandLine\Console;
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
    exit(0);
}

$codeCoverageCollector = new Collector();
foreach (CodeCoverageHelper::$defaultEngines as $engine) {
    $codeCoverageCollector->registerEngine(new $engine());
}
$codeCoverageCollector->registerFormatter(new PercentFormatter());
$coverageFormat = $options["--coverageFormat"];
if ($coverageFormat !== null) {
    $codeCoverageFormatter = new CodeCoverageHelper::$availableFormatters[$coverageFormat]();
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
    if (isset($options["--resultsFile"])) {
        $resultsFormatter->setOutputFileName($options["--resultsFile"]);
    }
}

$folderProvider = new TestsFolderProvider($options["path"]);

$extensions = [
    new CodeCoverageExtension($codeCoverageCollector),
    new ErrorsFilesExtension($folderProvider),
];

$console = new Console();
$console->useColors(isset($options["--colors"]));

$params = [
    "folderProvider" => $folderProvider,
    "extensions" => $extensions,
    "console" => $console,
];
if ($resultsFormatter !== null) {
    $params["resultsFormatter"] = $resultsFormatter;
}
$tester = new Tester(...$params);
$tester->execute();
