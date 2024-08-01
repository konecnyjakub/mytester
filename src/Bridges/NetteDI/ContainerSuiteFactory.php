<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteDI;

use MyTester\InvalidTestCaseException;
use MyTester\TestCase;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ContainerSuiteFactory implements \MyTester\ITestSuiteFactory
{
    /** @var TestCase[] */
    private array $suites;

    /**
     * @param TestCase[] $suites
     */
    public function __construct(array $suites)
    {
        $this->suites = $suites;
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
