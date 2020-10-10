<?php

declare(strict_types=1);

namespace MyTester\Bridges\NetteRobotLoader;

use MyTester\ITestsSuitesFinder;
use MyTester\TestCase;
use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;
use ReflectionClass;

/**
 * @author Jakub Konečný
 * @internal
 */
final class TestSuitesFinder implements ITestsSuitesFinder
{
    public function getSuites(string $folder): array
    {
        $suites = [];
        $robot = new RobotLoader();
        $tempDir = "$folder/temp/cache/Robot.Loader";
        FileSystem::createDir($tempDir);
        $robot->setTempDirectory($tempDir);
        $robot->addDirectory($folder);
        $robot->acceptFiles = ["*Test.php", ];
        $robot->rebuild();
        $robot->register();
        $classes = $robot->getIndexedClasses();
        foreach ($classes as $class => $file) {
            if (!class_exists($class)) {
                continue;
            }
            $rc = new ReflectionClass($class);
            if (!$rc->isAbstract() && $rc->isSubclassOf(TestCase::class)) {
                $suites[] = $rc->getName();
            }
        }
        return $suites;
    }
}
