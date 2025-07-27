<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionFunction;

/**
 * Report for code coverage
 * Contains all relevant data in a convenient form
 * Is a passed to {@see ICodeCoverageFormatter} to generate output
 *
 * @author Jakub Konečný
 */
final readonly class Report
{
    public int $linesTotal;
    public int $linesCovered;
    public int $coveragePercent;
    public string $sourcePath;
    /** @var ReportFile[] */
    public array $files;

    /**
     * @see ICodeCoverageEngine::collect()
     * @param array<string, array<int, int>> $data Raw code coverage data
     */
    public function __construct(array $data)
    {
        $filenames = array_keys($data);
        $this->sourcePath = Strings::findPrefix($filenames);

        $allClassNames = array_merge(get_declared_classes(), get_declared_traits());
        /** @var ReflectionClass<object>[] $allClasses */
        $allClasses = [];
        foreach ($allClassNames as $className) {
            $rc = new ReflectionClass($className);
            if (!str_starts_with((string) $rc->getFileName(), $this->sourcePath)) {
                continue;
            }
            $allClasses[] = $rc;
        }

        $allFunctionNames = get_defined_functions()["user"];
        /** @var ReflectionFunction[] $allFunctions */
        $allFunctions = [];
        foreach ($allFunctionNames as $functionName) {
            $rf = new ReflectionFunction($functionName);
            if (!str_starts_with((string) $rf->getFileName(), $this->sourcePath)) {
                continue;
            }
            $allFunctions[] = $rf;
        }

        /** @var ReportFile[] $files */
        $files = [];
        $totalLines = 0;
        $coveredLines = 0;
        foreach ($data as $filename => $file) {
            $classes = array_values(array_filter($allClasses, function (ReflectionClass $rc) use ($filename) {
                return ((string) $rc->getFileName() === $filename);
            }));
            $functions = array_values(array_filter($allFunctions, function (ReflectionFunction $rf) use ($filename) {
                return ((string) $rf->getFileName() === $filename);
            }));
            $files[] = new ReportFile(
                (string) Strings::after($filename, $this->sourcePath),
                $classes,
                $functions,
                $file
            );
            foreach ($file as $line) {
                $totalLines++;
                if ($line > 0) {
                    $coveredLines++;
                }
            }
        }
        $coveragePercent = ($totalLines === 0) ? 0 : (int) (($coveredLines / $totalLines) * 100);

        $this->coveragePercent = $coveragePercent;
        $this->linesTotal = $totalLines;
        $this->linesCovered = $coveredLines;
        $this->files = $files;
    }
}
