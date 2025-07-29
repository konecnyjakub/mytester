<?php
declare(strict_types=1);

require_once __DIR__ . "/functions.php";

require findVendorDirectory() . "/autoload.php";

use Konecnyjakub\PHPTRunner\PhpRunner;
use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\Annotations\Reader;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\ChainTestSuiteFactory;
use MyTester\ChainTestSuitesFinder;
use MyTester\CodeCoverage\CodeCoverageExtension;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\CodeCoverage\Formatters\PercentFormatter;
use MyTester\ComposerTestSuitesFinder;
use MyTester\ConsoleColors;
use MyTester\ErrorsFilesExtension;
use MyTester\InfoExtension;
use MyTester\PHPT\PHPTTestSuiteFactory;
use MyTester\PHPT\PHPTTestSuitesFinder;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
use MyTester\SimpleTestSuiteFactory;
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
    "--filterOnlyGroups" => [
        Parser::Argument => true,
        Parser::Optional => true,
        Parser::Default => "",
    ],
    "--filterExceptGroups" => [
        Parser::Argument => true,
        Parser::Optional => true,
        Parser::Default => "",
    ],
    "--filterExceptFolders" => [
        Parser::Argument => true,
        Parser::Optional => true,
        Parser::Default => "",
    ],
    "--version" => [
        Parser::Optional => true,
    ],
    "--noPhpt" => [
        Parser::Optional => true,
    ],
]);
/** @var array{path: string, "--colors"?: bool, "--coverageFormat"?: string, "--coverageFile"?: string, "--resultsFormat"?: string, "--resultsFile"?: string, "--filterOnlyGroups": string, "--filterExceptGroups": string,"--filterExceptFolders": string, "--version"?: bool, "--noPhpt"?: bool} $options */
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

$getArrayFromList = static function (string $value): array {
    if ($value === "") {
        return [];
    }
    if (!str_contains($value, ",")) {
        return [$value];
    }
    return explode(",", $value);
};

$folderProvider = new TestsFolderProvider($options["path"]);
$testSuitesSelectionCriteria = new \MyTester\TestSuitesSelectionCriteria(
    $folderProvider,
    onlyGroups: $getArrayFromList($options["--filterOnlyGroups"]),
    exceptGroups: $getArrayFromList($options["--filterExceptGroups"]),
    exceptFolders: $getArrayFromList($options["--filterExceptFolders"]),
);

$annotationsReader = Reader::create();
$testSuitesFinder = new ChainTestSuitesFinder();
$testSuitesFinder->registerFinder(new ComposerTestSuitesFinder($annotationsReader));
$testSuitesFinder->registerFinder(new TestSuitesFinder($annotationsReader));
$includePhptTests = !isset($options["--noPhpt"]);
if ($includePhptTests) {
    $testSuitesFinder->registerFinder(new PHPTTestSuitesFinder());
}

$testSuiteFactory = new ChainTestSuiteFactory();
if ($includePhptTests && class_exists(PhptRunner::class)) {
    $testSuiteFactory->registerFactory(new PHPTTestSuiteFactory(
        new PhptRunner(new \Konecnyjakub\PHPTRunner\Parser(), new PhpRunner()),
        $folderProvider
    ));
}
$testSuiteFactory->registerFactory(new SimpleTestSuiteFactory());

$console = new ConsoleColors();
$console->useColors = isset($options["--colors"]);

$extensions = [
    new CodeCoverageExtension($codeCoverageCollector),
    new ErrorsFilesExtension($folderProvider),
    new InfoExtension($console),
];

$params = [
    "testSuitesSelectionCriteria" => $testSuitesSelectionCriteria,
    "testSuitesFinder" => $testSuitesFinder,
    "testSuiteFactory" => $testSuiteFactory,
    "extensions" => $extensions,
    "console" => $console,
];
if ($resultsFormatter !== null) {
    $params["resultsFormatter"] = $resultsFormatter;
}
$tester = new Tester(...$params);
$tester->execute();
