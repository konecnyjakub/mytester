<?php
declare(strict_types=1);

namespace MyTester;

use MyTester\Annotations\Reader;
use ReflectionClass;
use ReflectionException;

/**
 * @author Jakub Konečný
 */
abstract class BaseTestSuitesFinder implements TestSuitesFinder
{
    protected Reader $annotationsReader;

    /**
     * @param class-string $class
     */
    protected function isTestSuite(string $class): bool
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException) { // @phpstan-ignore catch.neverThrown
            return false;
        }
        return !$reflection->isAbstract() && is_subclass_of($class, TestCase::class);
    }

    /**
     * @param class-string[] $testSuites
     * @return class-string[]
     */
    protected function applyFilters(array $testSuites, TestSuitesSelectionCriteria $criteria): array
    {
        return array_values(array_filter($testSuites, function (string $testSuite) use ($criteria): bool {
            $groups = $this->annotationsReader->getAnnotationMulti("group", $testSuite);
            foreach ($criteria->onlyGroups as $onlyGroup) {
                if (!in_array($onlyGroup, $groups, true)) {
                    return false;
                }
            }
            foreach ($criteria->exceptGroups as $exceptGroup) {
                if (in_array($exceptGroup, $groups, true)) {
                    return false;
                }
            }

            return true;
        }));
    }
}
