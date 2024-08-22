<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\Attributes\TestSuite;
use MyTester\InvalidTestCaseException;
use MyTester\TestCase;
use MyTester\Tester;

/**
 * Test suite for class ContainerFactory
 *
 * @author Jakub Konečný
 */
#[TestSuite("ContainerFactory")]
final class ContainerFactoryTest extends TestCase
{
    use TCompiledContainer;

    public function testCreate(): void
    {
        if (!isset($_ENV["MYTESTER_NETTE_DI"])) {
            $this->markTestSkipped();
        }
        $oldCallback = ContainerFactory::$onCreate;
        $oldParameters = ContainerFactory::create(false)->getParameters();

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
