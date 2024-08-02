<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

use Nette\Utils\Strings;
use ReflectionClass;

/**
 * Report for code coverage
 * Contains all relevant data in a convenient form
 * Is a passed to {@see ICodeCoverageFormatter} to generate output
 *
 * @author Jakub Konečný
 * @internal
 */
final readonly class Report
{
    use \Nette\SmartObject;

    public int $linesTotal;
    public int $linesCovered;
    public int $coveragePercent;
    public string $sourcePath;
    /** @var ReportFile[] */
    public array $files;

    /**
     * @param array $data Raw code coverage data created by {@see ICodeCoverageEngine::collect()}
     */
    public function __construct(array $data)
    {
        $filenames = array_keys($data);
        $this->sourcePath = Strings::findPrefix($filenames);

        $allClassNames = get_declared_classes();
        /** @var ReflectionClass[] $allClasses */
        $allClasses = [];
        foreach ($allClassNames as $className) {
            $rc = new ReflectionClass($className);
            if (!str_starts_with((string) $rc->getFileName(), $this->sourcePath)) {
                continue;
            }
            $allClasses[] = $rc;
        }

        /** @var ReportFile[] $files */
        $files = [];
        $totalLines = 0;
        $coveredLines = 0;
        foreach ($data as $filename => $file) {
            $classes = array_values(array_filter($allClasses, function (ReflectionClass $rc) use ($filename) {
                return ((string) $rc->getFileName() === $filename);
            }));
            $files[] = new ReportFile((string) Strings::after($filename, $this->sourcePath), $classes, $file);
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
