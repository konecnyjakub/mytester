<?php
declare(strict_types=1);

namespace MyTester;

/**
 * Results formatter for {@see Tester} that needs to know about folder with tests
 *
 * @author Jakub Konečný
 * @internal
 */
interface ITestFolderAwareResultsFormatter extends IResultsFormatter
{
    public function setTestsFolder(string $folder): void;
}
