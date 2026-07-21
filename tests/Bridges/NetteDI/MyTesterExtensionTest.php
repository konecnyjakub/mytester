<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\TestSuite;
use MyTester\TestCase;
use Nette\Application\LinkGenerator;
use Nette\DI\InvalidConfigurationException;

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
}
