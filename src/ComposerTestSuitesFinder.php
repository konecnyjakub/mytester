<?php
declare(strict_types=1);

namespace MyTester;

/**
 * @author Jakub Konečný
 * @internal
 */
final class ComposerTestSuitesFinder extends BaseTestSuitesFinder
{
    public function getSuites(string $folder): array
    {
        $suites = [];
        $folder = (string) realpath($folder);
        $classMap = require \findVendorDirectory() . "/composer/autoload_classmap.php";
        foreach ($classMap as $class => $file) {
            $file = (string) realpath($file);
            if (!str_starts_with($file, $folder) || !str_ends_with($file, static::FILENAME_SUFFIX)) {
                continue;
            }
            if ($this->isTestSuite($class)) {
                $suites[] = $class;
            }
        }
        return $suites;
    }
}
