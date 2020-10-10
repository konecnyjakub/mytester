<?php

declare(strict_types=1);


namespace MyTester;

use ReflectionClass;
use ReflectionException;

/**
 * @author Jakub Konečný
 * @internal
 */
class ComposerTestsSuitesFinder implements ITestsSuitesFinder
{

    public function getSuites(string $folder): array
    {
        $suites = [];
        $folder = (string) realpath($folder);
        $classMap = require \findVendorDirectory() . "/composer/autoload_classmap.php";
        foreach ($classMap as $class => $file) {
            $file = (string) realpath($file);
            if (!str_starts_with($file, $folder)) {
                continue;
            }
            try {
                $reflection = new ReflectionClass($class);
            } catch (ReflectionException $e) {
                continue;
            }
            if (!$reflection->isAbstract() && $reflection->isSubclassOf(TestCase::class)) {
                $suites[] = $reflection->getName();
            }
        }
        return $suites;
    }
}
