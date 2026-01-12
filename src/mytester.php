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
use MyTester\CodeCoverage\Formatters\PercentFormatter;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\ComposerTestSuitesFinder;
use MyTester\Config\CliArgumentsConfigAdapter;
use MyTester\Config\ConfigResolver;
use MyTester\ConsoleColors;
use MyTester\ErrorsFilesExtension;
use MyTester\InfoExtension;
use MyTester\PHPT\PHPTTestSuiteFactory;
use MyTester\PHPT\PHPTTestSuitesFinder;
use MyTester\SimpleTestSuiteFactory;
use MyTester\Tester;
use Nette\CommandLine\Parser;

$cmd = new Parser();
$cmd->addArgument(CliArgumentsConfigAdapter::ARGUMENT_PATH, optional: true)
    ->addSwitch(CliArgumentsConfigAdapter::ARGUMENT_COLORS)
    ->addOption("--coverage", repeatable: true)
    ->addOption(CliArgumentsConfigAdapter::ARGUMENT_RESULTS, repeatable: true)
    ->addOption(CliArgumentsConfigAdapter::ARGUMENT_FILTER_ONLY_GROUPS, optionalValue: true, fallback: "")
    ->addOption(CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_GROUPS, optionalValue: true, fallback: "")
    ->addOption(CliArgumentsConfigAdapter::ARGUMENT_FILTER_EXCEPT_FOLDERS, optionalValue: true, fallback: "")
    ->addSwitch(CliArgumentsConfigAdapter::ARGUMENT_NO_PHPT)
    ->addSwitch("--version");

if ($cmd->parseOnly(["--version"])["--version"] === true) {
    echo InfoExtension::getTesterVersion() . "\n";
    exit(0);
}

/** @var array{path?: string, "--colors"?: bool, "--coverage": string[], "--results": string[], "--filterOnlyGroups": string, "--filterExceptGroups": string,"--filterExceptFolders": string, "--version"?: bool, "--noPhpt"?: bool} $options */
$options = $cmd->parse();

$codeCoverageCollector = new Collector();
foreach (CodeCoverageHelper::$defaultEngines as $engine) {
    $codeCoverageCollector->registerEngine(new $engine());
}
$codeCoverageCollector->registerFormatter(new PercentFormatter());
foreach ($options["--coverage"] as $coverage) {
    $coverage = explode(":", $coverage, 2);
    if (!array_key_exists($coverage[0], CodeCoverageHelper::$availableFormatters)) {
        throw new ValueError("Unknown code coverage formatter " . $coverage[0]);
    }
    $codeCoverageFormatter = new CodeCoverageHelper::$availableFormatters[$coverage[0]]();
    if (
        $codeCoverageFormatter instanceof \MyTester\CodeCoverage\CodeCoverageCustomFileNameFormatter &&
        isset($coverage[1])
    ) {
        $codeCoverageFormatter->setOutputFileName($coverage[1]);
    }
    $codeCoverageCollector->registerFormatter($codeCoverageFormatter);
}

$config = new ConfigResolver();
$config->addAdapter(new CliArgumentsConfigAdapter($options));
$folderProvider = $config->getTestsFolderProvider();
$testSuitesSelectionCriteria = $config->getTestSuitesSelectionCriteria();
$resultsFormatters = $config->getResultsFormatters();

$annotationsReader = Reader::create();
$testSuitesFinder = new ChainTestSuitesFinder();
$testSuitesFinder->registerFinder(new ComposerTestSuitesFinder($annotationsReader));
$testSuitesFinder->registerFinder(new TestSuitesFinder($annotationsReader));
if ($config->getIncludePhptTests()) {
    $testSuitesFinder->registerFinder(new PHPTTestSuitesFinder());
}

$testSuiteFactory = new ChainTestSuiteFactory();
if ($config->getIncludePhptTests() && class_exists(PhptRunner::class)) {
    $testSuiteFactory->registerFactory(new PHPTTestSuiteFactory(
        new PhptRunner(new \Konecnyjakub\PHPTRunner\Parser(), new PhpRunner()),
        $folderProvider,
        $testSuitesSelectionCriteria
    ));
}
$testSuiteFactory->registerFactory(new SimpleTestSuiteFactory());

$console = new ConsoleColors();
$console->useColors = $config->getUseColors();

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
if (count($resultsFormatters) > 0) {
    $params["resultsFormatters"] = $resultsFormatters;
}
$tester = new Tester(...$params);
$tester->execute();
