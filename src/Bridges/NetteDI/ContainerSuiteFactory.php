<?php

declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\InvalidTestCaseException;
use MyTester\TestCase;
use Nette\DI\Container;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ContainerSuiteFactory implements \MyTester\ITestSuiteFactory
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

  /**
   * @param class-string $className
   */
    public function create(string $className): TestCase
    {
        $suit = $this->container->getByType($className);
        if (!$suit instanceof TestCase) {
            throw new InvalidTestCaseException("$className is not a descendant of " . TestCase::class . ".");
        }
        return $suit;
    }
}
