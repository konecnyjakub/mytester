<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Test suites finder for {@see Tester}
 *
 * Uses Composer's autoloader
 *
 * @author Jakub Konečný
 */
final class ComposerTestSuitesFinder extends BaseTestSuitesFinder
{
    public function getSuites(TestSuitesSelectionCriteria $criteria): array
    {
        $suites = [];
        $folder = (string) realpath($criteria->testsFolderProvider->folder);
        /** @var array<class-string, string> $classMap */
        $classMap = require \findVendorDirectory() . "/composer/autoload_classmap.php";
        foreach ($classMap as $class => $file) {
            $file = (string) realpath($file);
            if (!str_starts_with($file, $folder) || !str_ends_with($file, $criteria->filenameSuffix)) {
                continue;
            }
            if ($this->isTestSuite($class)) {
                $suites[] = $class;
            }
        }
        return $suites;
    }
}
