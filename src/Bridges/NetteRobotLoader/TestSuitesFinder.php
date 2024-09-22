<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteRobotLoader;

use MyTester\BaseTestSuitesFinder;
use Nette\Loaders\RobotLoader;
use Nette\Utils\FileSystem;

/**
 * Test suites finder for {@see Tester}
 * Uses Nette RobotLoader
 *
 * @author Jakub Konečný
 */
final class TestSuitesFinder extends BaseTestSuitesFinder
{
    public function getSuites(string $folder): array
    {
        if (!$this->isAvailable()) {
            return [];
        }
        $suites = [];
        $robot = new RobotLoader();
        $tempDir = "$folder/temp/cache/Robot.Loader";
        FileSystem::createDir($tempDir);
        $robot->setTempDirectory($tempDir);
        $robot->addDirectory($folder);
        $robot->acceptFiles = ["*" . static::FILENAME_SUFFIX, ];
        $robot->rebuild();
        $robot->register();
        $classes = $robot->getIndexedClasses();
        foreach ($classes as $class => $file) {
            if ($this->isTestSuite($class)) {
                $suites[] = $class;
            }
        }
        return $suites;
    }

    private function isAvailable(): bool
    {
        return class_exists(RobotLoader::class);
    }
}
