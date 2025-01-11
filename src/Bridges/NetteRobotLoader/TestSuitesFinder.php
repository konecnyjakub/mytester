<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteRobotLoader;

use MyTester\BaseTestSuitesFinder;
use MyTester\TestSuitesSelectionCriteria;
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
    public function getSuites(TestSuitesSelectionCriteria $criteria): array
    {
        if (!$this->isAvailable()) {
            return [];
        }
        $folder = $criteria->testsFolderProvider->folder;
        $suites = [];
        $robot = new RobotLoader();
        $tempDir = "$folder/temp/cache/Robot.Loader";
        FileSystem::createDir($tempDir);
        $robot->setTempDirectory($tempDir);
        $robot->addDirectory($folder);
        $robot->acceptFiles = ["*" . $criteria->filenameSuffix, ];
        $robot->rebuild();
        $robot->register();
        $classes = $robot->getIndexedClasses();
        /**
         * @var class-string $class
         */
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
