<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\TestSuite;
use MyTester\CodeCoverage\Formatters\TextFormatter;
use MyTester\ResultsFormatters\Tap;
use MyTester\TestCase;
use Nette\Application\LinkGenerator;
use Nette\DI\InvalidConfigurationException;
use ValueError;

/**
 * Test suite for class MyTesterExtension
 *
 * @author Jakub Konečný
 */
#[TestSuite("MyTesterExtension")]
#[Group("nette")]
#[Group("netteDI")]
#[RequiresEnvVariable("MYTESTER_NETTE_DI")]
final class MyTesterExtensionTest extends TestCase
{
    use TCompiledContainer;

    public function testUrl(): void
    {
        $linkGenerator = $this->getService(LinkGenerator::class);
        $this->assertSame("http:/", $linkGenerator->link("Test:"));

        $this->assertThrowsException(function () {
            $this->refreshContainer([
                "mytester" => [
                    "url" => "abc.def"
                ],
            ], false);
        }, InvalidConfigurationException::class);

        $container = $this->refreshContainer([
            "mytester" => [
                "url" => "https://mytester.localhost"
            ],
        ], false);
        $linkGenerator = $container->getByType(LinkGenerator::class);
        $this->assertSame("https://mytester.localhost/test/new", $linkGenerator->link("Test:new"));
    }

    public function testCodeCoverageFormatters(): void
    {
        $this->assertThrowsException(function () {
            $this->refreshContainer([
                "mytester" => [
                    "coverage" => [
                        "abc"
                    ]
                ]
            ], false);
        }, ValueError::class, "Unknown code coverage formatter abc");

        $container = $this->refreshContainer([
            "mytester" => [
                "coverage" => [
                    "text:report.txt"
                ]
            ],
        ], false);
        $formatters = $container->findByTag(MyTesterExtension::TAG_COVERAGE_FORMATTER);
        $this->assertCount(3, $formatters);
        $this->assertArrayHasKey("mytester.coverage.formatter.percent", $formatters);
        $this->assertArrayHasKey("mytester.coverage.formatter.cobertura", $formatters);
        $this->assertArrayHasKey("mytester.coverage.formatter.text", $formatters);
        $textFormatter = $container->getByType(TextFormatter::class);
        $this->assertSame("abc/report.txt", $textFormatter->getOutputFileName("abc"));
    }

    public function testResultsFormatters(): void
    {
        $this->assertThrowsException(function () {
            $this->refreshContainer([
                "mytester" => [
                    "results" => [
                        "abc"
                    ]
                ]
            ], false);
        }, ValueError::class, "Unknown results formatter abc");

        $container = $this->refreshContainer([
            "mytester" => [
                "results" => [
                    "tap:report.txt"
                ]
            ],
        ], false);
        $formatters = $container->findByTag(MyTesterExtension::TAG_RESULTS_FORMATTER);
        $this->assertCount(2, $formatters);
        $this->assertArrayHasKey("mytester.resultsFormatter.console", $formatters);
        $this->assertArrayHasKey("mytester.resultsFormatter.tap", $formatters);
        $tapFormatter = $container->getByType(Tap::class);
        $this->assertSame("abc/report.txt", $tapFormatter->getOutputFileName("abc"));
    }
}
