<?php
declare(strict_types=1);

namespace MyTester\PHPT;

use Konecnyjakub\PHPTRunner\PhptRunner;
use MyTester\TestSuitesFinder;
use MyTester\TestSuitesSelectionCriteria;
use Nette\Utils\Finder;

/**
 * Test suites finder for {@see Tester}
 *
 * Allows using .phpt files as test suites
 *
 * @author Jakub Konečný
 */
final class PHPTTestSuitesFinder implements TestSuitesFinder
{
    public function getSuites(TestSuitesSelectionCriteria $criteria): array
    {
        $suites = [];
        if ($this->isAvailable() && $this->hasPhptFiles($criteria->testsFolderProvider->folder)) {
            $suites[] = PHPTTestCase::class;
        }
        return $suites;
    }

    private function isAvailable(): bool
    {
        return class_exists(PhptRunner::class);
    }

    private function hasPhptFiles(string $folder): bool
    {
        $files = Finder::findFiles("*.phpt")->from($folder)->collect();
        return count($files) > 0;
    }
}
