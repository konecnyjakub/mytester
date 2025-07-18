<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\TestSuite;
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
        $oldParameters = ContainerFactory::create()->getParameters();

        $var = 0;
        $callback = function () use (&$var) {
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
        ContainerFactory::create(true, $oldParameters);
    }
}
