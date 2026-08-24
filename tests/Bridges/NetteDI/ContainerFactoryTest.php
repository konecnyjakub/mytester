<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\TestSuite;
use MyTester\ResultsFormatters\Console;
use MyTester\ResultsFormatters\Tap;
use MyTester\TestCase;
use MyTester\Tester;

/**
 * Test suite for class ContainerFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("ContainerFactory")]
#[Group("nette")]
#[Group("netteDI")]
#[RequiresEnvVariable("MYTESTER_NETTE_DI")]
final class ContainerFactoryTest extends TestCase
{
    use TCompiledContainer;

    public function testCreate(): void
    {
        $oldCallback = ContainerFactory::$onCreate;

        $var = 0;
        $callback = static function () use (&$var) {
            $var++;
        };

        ContainerFactory::$onCreate = $callback;
        $this->refreshContainer();
        $this->assertSame(1, $var);
        $this->assertType(Tester::class, $this->getService(Tester::class));

        ContainerFactory::$onCreate = $callback;
        $this->getContainer();
        $this->assertSame(1, $var);

        ContainerFactory::$onCreate = $oldCallback;
        ContainerFactory::create(true);
        $this->assertSame(1, $var);

        $oldContainer = $this->getContainer();
        $newContainer = $this->refreshContainer([], false);
        $this->assertNotSame($oldContainer, $newContainer);
        $this->assertSame($oldContainer, ContainerFactory::create());

        $formatters = $oldContainer->findByTag(MyTesterExtension::TAG_RESULTS_FORMATTER);
        $this->assertCount(1, $formatters);
        $this->assertArrayHasKey("mytester.resultsFormatter.console", $formatters);
        $newContainer = $this->refreshContainer([
            "mytester" => [
                "results" => [
                    "tap"
                ],
            ],
        ], false);
        $formatters = $newContainer->findByTag(MyTesterExtension::TAG_RESULTS_FORMATTER);
        $this->assertCount(2, $formatters);
        $this->assertArrayHasKey("mytester.resultsFormatter.console", $formatters);
        $this->assertArrayHasKey("mytester.resultsFormatter.tap", $formatters);
    }
}
