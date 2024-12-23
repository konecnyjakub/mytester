<?php
declare(strict_types=1);

require_once __DIR__ . "/functions.php";

require findVendorDirectory() . "/autoload.php";

use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\ChainTestSuitesFinder;
use MyTester\CodeCoverage\CodeCoverageExtension;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\CodeCoverage\Formatters\PercentFormatter;
use MyTester\ComposerTestSuitesFinder;
use MyTester\ConsoleColors;
use MyTester\ErrorsFilesExtension;
use MyTester\InfoExtension;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
use MyTester\Tester;
use MyTester\TestsFolderProvider;
use Nette\CommandLine\Parser;

$cmd = new Parser("", [
    "path" => [
        Parser::Default => getcwd() . "/tests",
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
/** @var array{path: string, "--colors"?: bool, "--coverageFormat"?: string, "--coverageFile"?: string, "--resultsFormat"?: string, "--resultsFile"?: string, "--version"?: bool} $options */
$options = $cmd->parse();

if (isset($options["--version"])) {
    echo InfoExtension::getTesterVersion() . "\n";
    exit(0);
}

$codeCoverageCollector = new Collector();
foreach (CodeCoverageHelper::$defaultEngines as $engine) {
    $codeCoverageCollector->registerEngine(new $engine());
}
$codeCoverageCollector->registerFormatter(new PercentFormatter());
if (isset($options["--coverageFormat"])) {
    $codeCoverageFormatter = new CodeCoverageHelper::$availableFormatters[$options["--coverageFormat"]]();
    if (
        $codeCoverageFormatter instanceof \MyTester\CodeCoverage\ICodeCoverageCustomFileNameFormatter &&
        isset($options["--coverageFile"])
    ) {
        $codeCoverageFormatter->setOutputFileName($options["--coverageFile"]);
    }
    $codeCoverageCollector->registerFormatter($codeCoverageFormatter);
}

$resultsFormatter = null;
if (isset($options["--resultsFormat"])) {
    $type = ResultsHelper::$availableFormatters[$options["--resultsFormat"]];
    /** @var \MyTester\IResultsFormatter $resultsFormatter */
    $resultsFormatter = new $type();
    if (isset($options["--resultsFile"])) {
        $resultsFormatter->setOutputFileName($options["--resultsFile"]);
    }
}

$folderProvider = new TestsFolderProvider($options["path"]);

$testSuitesFinder = new ChainTestSuitesFinder();
$testSuitesFinder->registerFinder(new ComposerTestSuitesFinder());
$testSuitesFinder->registerFinder(new TestSuitesFinder());

$console = new ConsoleColors();
$console->useColors = isset($options["--colors"]);

$extensions = [
    new CodeCoverageExtension($codeCoverageCollector),
    new ErrorsFilesExtension($folderProvider),
    new InfoExtension($console),
];

$params = [
    "folderProvider" => $folderProvider,
    "testSuitesFinder" => $testSuitesFinder,
    "extensions" => $extensions,
    "console" => $console,
];
if ($resultsFormatter !== null) {
    $params["resultsFormatter"] = $resultsFormatter;
}
$tester = new Tester(...$params);
$tester->execute();
