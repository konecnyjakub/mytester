<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\InvalidTestCaseException;
use MyTester\TestCase;

/**
 * Test suites factory for {@see Tester}
 *
 * Tries to get instance of given class from Nette DI container
 *
 * @author Jakub Konečný
 */
final readonly class ContainerSuiteFactory implements \MyTester\ITestSuiteFactory
{
    /**
     * @param TestCase[] $suites
     */
    public function __construct(private array $suites)
    {
    }

  /**
   * @param class-string $className
   */
    public function create(string $className): TestCase
    {
        foreach ($this->suites as $suite) {
            if ($suite instanceof $className && $suite instanceof TestCase) {
                return $suite;
            }
        }
        throw new InvalidTestCaseException("$className is not a descendant of " . TestCase::class . ".");
    }
}
