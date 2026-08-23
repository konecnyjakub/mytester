<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use Exception;
use MyTester\Annotations\Reader;
use MyTester\Bridges\NetteApplication\PresenterMock;
use MyTester\Bridges\NetteHttp\FakeSession;
use MyTester\Bridges\NetteRobotLoader\TestSuitesFinder;
use MyTester\CodeCoverage\CodeCoverageCustomFileNameFormatter;
use MyTester\CodeCoverage\CodeCoverageExtension;
use MyTester\CodeCoverage\Collector;
use MyTester\CodeCoverage\Helper as CodeCoverageHelper;
use MyTester\ConsoleColors;
use MyTester\ErrorsFilesExtension;
use MyTester\InfoExtension;
use MyTester\TesterExtension;
use MyTester\ResultsFormatters\Helper as ResultsHelper;
use MyTester\Tester;
use MyTester\TestsFolderProvider;
use MyTester\TestSuitesSelectionCriteria;
use Nette\Application\LinkGenerator;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\DI\Helpers;
use Nette\Http\Session;
use Nette\Http\UrlScript;
use Nette\Schema\Expect;
use Nette\Utils\Validators;
use ValueError;

/**
 * MyTester Extension for Nette DIC
 *
 * @author Jakub Konečný
 * @method Config getConfig()
 */
final class MyTesterExtension extends \Nette\DI\CompilerExtension
{
    public const string TAG_TEST = "mytester.test";
    public const string TAG_EXTENSION = "mytester.extension";
    public const string TAG_RESULTS_FORMATTER = "mytester.resultsFormatter";
    public const string TAG_COVERAGE_ENGINE = "mytester.coverage.engine";
    public const string TAG_COVERAGE_FORMATTER = "mytester.coverage.formatter";
    private const string SERVICE_RUNNER = "runner";
    private const string SERVICE_ANNOTATIONS_READER = "annotationsReader";
    private const string SERVICE_TEST_SUITES_SELECTION_CRITERIA = "testSuitesSelectionCriteria";
    private const string SERVICE_TEST_SUITES_FINDER = "testSuitesFinder";
    private const string SERVICE_SUITE_FACTORY = "suiteFactory";
    private const string SERVICE_RESULTS_FORMATTER_PREFIX = "resultsFormatter";
    private const string SERVICE_EXTENSION_PREFIX = "extension.";
    private const string SERVICE_CC_COLLECTOR = "coverage.collector";
    private const string SERVICE_CC_ENGINE_PREFIX = "coverage.engine.";
    private const string SERVICE_CC_FORMATTER_PREFIX = "coverage.formatter";
    private const string SERVICE_PRESENTER_MOCK = "presenterMock";
    private const string SERVICE_TESTS_FOLDER_PROVIDER = "testsFolderProvider";
    private const string SERVICE_CONSOLE_WRITER = "consoleWriter";

    /** @var array<string, class-string>  */
    private array $codeCoverageFormatters = [];

    public function getConfigSchema(): \Nette\Schema\Schema
    {
        $params = $this->getContainerBuilder()->parameters;
        return Expect::from(new Config(), [
            "folder" => Expect::string(Helpers::expand("%appDir%/../tests", $params))
                ->assert(is_dir(...), "Invalid folder"), // @phpstan-ignore argument.type
            "extensions" => Expect::arrayOf("class")
                // @phpstan-ignore argument.type
                ->assert(static fn(string $classname): bool => is_subclass_of($classname, TesterExtension::class)),
            "coverageFormat" => Expect::anyOf(
                null,
                ...array_keys(CodeCoverageHelper::$availableFormatters)
            )->deprecated("The item %path% is deprecated, use coverage instead."),
            "coverage" => Expect::listOf("string")->default(["percent"]),
            "resultsFormat" => Expect::anyOf(
                ...array_keys(ResultsHelper::$availableFormatters)
            )->default("console")->deprecated("The item %path% is deprecated, use results instead."),
            "results" => Expect::listOf("string"),
            "url" => Expect::string("")
                // @phpstan-ignore argument.type
                ->assert(static fn (string $url) => $url === "" || Validators::isUrl($url)),
        ]);
    }

    /**
     * @throws Exception
     */
    public function loadConfiguration(): void
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix(self::SERVICE_TESTS_FOLDER_PROVIDER))
            ->setFactory(TestsFolderProvider::class, [$config->folder]);

        $builder->addDefinition($this->prefix(self::SERVICE_RUNNER))
            ->setType(Tester::class);

        $builder->addDefinition($this->prefix(self::SERVICE_SUITE_FACTORY))
            ->setType(ContainerSuiteFactory::class);

        $builder->addDefinition($this->prefix(self::SERVICE_PRESENTER_MOCK))
            ->setType(PresenterMock::class)
            ->setAutowired(PresenterMock::class);

        $extensions = array_merge(
            [CodeCoverageExtension::class, ErrorsFilesExtension::class, InfoExtension::class, ],
            $config->extensions
        );
        foreach ($extensions as $index => $extension) {
            $builder->addDefinition($this->prefix(self::SERVICE_EXTENSION_PREFIX . ($index + 1)))
                ->setType($extension)
                ->addTag(self::TAG_EXTENSION);
        }

        $builder->addDefinition($this->prefix(self::SERVICE_ANNOTATIONS_READER))
            ->setFactory(Reader::class . "::create");

        $builder->addDefinition($this->prefix(self::SERVICE_TEST_SUITES_SELECTION_CRITERIA))
            ->setType(TestSuitesSelectionCriteria::class)
            ->setArgument("onlyGroups", $config->filterOnlyGroups)
            ->setArgument("exceptGroups", $config->filterExceptGroups)
            ->setArgument("exceptFolders", $config->filterExceptFolders);

        $builder->addDefinition($this->prefix(self::SERVICE_TEST_SUITES_FINDER))
            ->setType(TestSuitesFinder::class);
        $suites = (new TestSuitesFinder(Reader::create()))->getSuites(
            new TestSuitesSelectionCriteria(new TestsFolderProvider($config->folder))
        );
        foreach ($suites as $index => $suite) {
            $builder->addDefinition($this->prefix("test." . ($index + 1)))
                ->setType($suite)
                ->addTag(self::TAG_TEST);
        }

        $builder->addDefinition($this->prefix(self::SERVICE_CC_COLLECTOR))
            ->setType(Collector::class);
        foreach (CodeCoverageHelper::$defaultEngines as $name => $className) {
            $builder->addDefinition($this->prefix(self::SERVICE_CC_ENGINE_PREFIX . $name))
                ->setType($className)
                ->addTag(self::TAG_COVERAGE_ENGINE);
        }
        $coverageFormat = $config->coverageFormat;
        if ($coverageFormat !== null) {
            $this->codeCoverageFormatters[$coverageFormat] = CodeCoverageHelper::$availableFormatters[$coverageFormat];
        }
        foreach ($this->codeCoverageFormatters as $name => $className) {
            $builder->addDefinition($this->prefix(self::SERVICE_CC_FORMATTER_PREFIX . "." . $name))
                ->setType($className)
                ->addTag(self::TAG_COVERAGE_FORMATTER);
        }
        foreach ($config->coverage as $coverage) {
            $coverage = explode(":", $coverage, 2);
            if (!array_key_exists($coverage[0], CodeCoverageHelper::$availableFormatters)) {
                throw new ValueError("Unknown code coverage formatter " . $coverage[0]);
            }
            $coverageClass = CodeCoverageHelper::$availableFormatters[$coverage[0]];
            $codeCoverageFormatterService = $builder->addDefinition(
                $this->prefix(self::SERVICE_CC_FORMATTER_PREFIX . "." . $coverage[0])
            )
                ->setType($coverageClass)
                ->addTag(self::TAG_COVERAGE_FORMATTER);
            if (is_a($coverageClass, CodeCoverageCustomFileNameFormatter::class, true) && isset($coverage[1])) {
                $codeCoverageFormatterService->addSetup("setOutputFileName", [$coverage[1]]);
            }
        }

        $builder->addDefinition($this->prefix(self::SERVICE_RESULTS_FORMATTER_PREFIX))
            ->setType(ResultsHelper::$availableFormatters[$config->resultsFormat])
            ->addTag(self::TAG_RESULTS_FORMATTER);
        foreach ($config->results as $results) {
            $results = explode(":", $results, 2);
            if (!array_key_exists($results[0], ResultsHelper::$availableFormatters)) {
                throw new ValueError("Unknown results formatter " . $results[0]);
            }
            $resultsFormatterService = $builder->addDefinition(
                $this->prefix(self::SERVICE_RESULTS_FORMATTER_PREFIX . "." . $results[0])
            )->setType(ResultsHelper::$availableFormatters[$results[0]])
                ->addTag(self::TAG_RESULTS_FORMATTER);
            if (isset($results[1])) {
                $resultsFormatterService->addSetup("setOutputFileName", [$results[1]]);
            }
        }

        $builder->addDefinition($this->prefix(self::SERVICE_CONSOLE_WRITER))
            ->setType(ConsoleColors::class)
            ->addSetup('$service->useColors = ?', [$config->colors]);
    }

    public function beforeCompile(): void
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();
        /** @var ServiceDefinition $coverageCollector */
        $coverageCollector = $builder->getDefinition($this->prefix(self::SERVICE_CC_COLLECTOR));

        foreach ($builder->findByTag(self::TAG_COVERAGE_ENGINE) as $serviceName => $tagValue) {
            $coverageCollector->addSetup("registerEngine", ["@$serviceName", ]);
        }

        foreach ($builder->findByTag(self::TAG_COVERAGE_FORMATTER) as $serviceName => $tagValue) {
            $coverageCollector->addSetup("registerFormatter", ["@$serviceName", ]);
        }

        $originalSessionName = $builder->getByType(Session::class);
        if (is_string($originalSessionName)) {
            $originalSession = $builder->getDefinition($originalSessionName);
            $builder->removeDefinition($originalSessionName);
            $builder->addDefinition($this->prefix("originalSession"), clone $originalSession)
                ->setAutowired(false);
            $builder->addDefinition($originalSessionName)
                ->setType(Session::class)
                ->setFactory(FakeSession::class, [$this->prefix("@originalSession"),]);
        }

        $linkGeneratorName = $builder->getByType(LinkGenerator::class);
        if ($config->url !== "" && $linkGeneratorName !== null) {
            /** @var ServiceDefinition $linkGenerator */
            $linkGenerator = $builder->getDefinition($linkGeneratorName);
            $linkGenerator->setArgument("refUrl", new Statement(UrlScript::class, [$config->url]));
        }
    }
}
