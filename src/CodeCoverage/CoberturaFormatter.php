<?php
declare(strict_types=1);

namespace MyTester\CodeCoverage;

/**
 * Cobertura formatter for code coverage
 * Returns XML code according to Cobertura specification
 * @see https://raw.githubusercontent.com/cobertura/web/master/htdocs/xml/coverage-04.dtd
 *
 * @author Jakub Konečný
 * @internal
 */
final class CoberturaFormatter implements ICodeCoverageCustomFileNameFormatter
{
    private string $baseFileName = "coverage.xml";

    public function render(Report $report): string
    {
        if (!extension_loaded("dom")) {
            return "";
        }

        $implementation = new \DOMImplementation();
        $documentType = $implementation->createDocumentType(
            "coverage",
            "",
            "http://cobertura.sourceforge.net/xml/coverage-04.dtd"
        );

        $document = $implementation->createDocument("", "", $documentType);
        $document->xmlVersion = "1.0";
        $document->encoding = "utf-8";
        $document->formatOutput = true;

        $coverage = $document->createElement("coverage");
        $coverage->setAttribute("lines-valid", (string) $report->linesTotal);
        $coverage->setAttribute("lines-covered", (string) $report->linesCovered);
        $coverage->setAttribute("line-rate", (string) $report->coveragePercent);
        $coverage->setAttribute("branches-valid", (string) 0);
        $coverage->setAttribute("branches-covered", (string) 0);
        $coverage->setAttribute("branch-rate", (string) 0);
        $coverage->setAttribute("complexity", "");
        $coverage->setAttribute("version", "0.4");
        $coverage->setAttribute("timestamp", (string) time());
        $document->appendChild($coverage);

        $sources = $document->createElement("sources");
        $source = $document->createElement("source", $report->sourcePath);
        $sources->appendChild($source);
        $coverage->appendChild($sources);

        $packages = $document->createElement("packages");
        foreach ($report->files as $reportFile) {
            $package = $document->createElement("package");
            $package->setAttribute("name", $reportFile->name);
            $package->setAttribute("line-rate", (string) $reportFile->coveragePercent);
            $package->setAttribute("branch-rate", (string) 0);
            $package->setAttribute("complexity", "");
            $classes = $document->createElement("classes");

            foreach ($reportFile->classes as $reflectionClass) {
                $classLines = array_filter($reportFile->data, function (int $line) use ($reflectionClass) {
                    return ($line >= $reflectionClass->getStartLine() && $line <= $reflectionClass->getEndLine());
                }, ARRAY_FILTER_USE_KEY);
                $totalLines = count($classLines);
                $coveredLines = count(array_filter($classLines, function (int $value) {
                    return $value > 0;
                }));
                $coveragePercent = ($totalLines === 0) ? 0 : (int) (($coveredLines / $totalLines) * 100);

                $class = $document->createElement("class");
                $class->setAttribute("name", $reflectionClass->getName());
                $class->setAttribute("filename", $reportFile->name);
                $class->setAttribute("line-rate", (string) $coveragePercent);
                $class->setAttribute("branch-rate", (string) 0);
                $class->setAttribute("complexity", (string) 0);

                $methods = $document->createElement("methods");
                foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                    if ($reflectionMethod->getFileName() !== $report->sourcePath . $reportFile->name) {
                        continue;
                    }
                    $methodLines = array_filter($reportFile->data, function (int $line) use ($reflectionMethod) {
                        return ($line >= $reflectionMethod->getStartLine() && $line <= $reflectionMethod->getEndLine());
                    }, ARRAY_FILTER_USE_KEY);
                    $totalLines = count($methodLines);
                    $coveredLines = count(array_filter($methodLines, function (int $value) {
                        return $value > 0;
                    }));
                    $coveragePercent = ($totalLines === 0) ? 0 : (int) (($coveredLines / $totalLines) * 100);

                    $method = $document->createElement("method");
                    $method->setAttribute("name", $reflectionMethod->getName());
                    $method->setAttribute("signature", "");
                    $method->setAttribute("line-rate", (string) $coveragePercent);
                    $method->setAttribute("branch-rate", (string) 0);

                    $lines = $document->createElement("lines");
                    foreach ($methodLines as $lineNumber => $hits) {
                        $line = $document->createElement("line");
                        $line->setAttribute("number", (string) $lineNumber);
                        $line->setAttribute("hits", (string) max(0, $hits));
                        $lines->appendChild($line);
                    }
                    $method->appendChild($lines);

                    $methods->appendChild($method);
                }
                $class->appendChild($methods);

                $lines = $document->createElement("lines");
                foreach ($classLines as $lineNumber => $hits) {
                    $line = $document->createElement("line");
                    $line->setAttribute("number", (string) $lineNumber);
                    $line->setAttribute("hits", (string) max(0, $hits));
                    $lines->appendChild($line);
                }
                $class->appendChild($lines);

                $classes->appendChild($class);
            }

            if (count($reportFile->functions) > 0) {
                $class = $document->createElement("class");
                $class->setAttribute("name", $reportFile->name);
                $class->setAttribute("filename", $reportFile->name);
                $class->setAttribute("branch-rate", (string) 0);
                $class->setAttribute("complexity", (string) 0);

                $totalLinesClass = 0;
                $coveredLinesClass = 0;

                $methods = $document->createElement("methods");
                foreach ($reportFile->functions as $function) {
                    $functionLines = array_filter($reportFile->data, function (int $line) use ($function) {
                        return ($line >= $function->getStartLine() && $line <= $function->getEndLine());
                    }, ARRAY_FILTER_USE_KEY);
                    $totalLines = count($functionLines);
                    $totalLinesClass += $totalLines;
                    $coveredLines = count(array_filter($functionLines, function (int $value) {
                        return $value > 0;
                    }));
                    $coveredLinesClass += $coveredLines;
                    $coveragePercent = ($totalLines === 0) ? 0 : (int) (($coveredLines / $totalLines) * 100);

                    $method = $document->createElement("method");
                    $method->setAttribute("name", $function->getName());
                    $method->setAttribute("signature", "");
                    $method->setAttribute("line-rate", (string) $coveragePercent);
                    $method->setAttribute("branch-rate", (string) 0);

                    $lines = $document->createElement("lines");
                    foreach ($functionLines as $lineNumber => $hits) {
                        $line = $document->createElement("line");
                        $line->setAttribute("number", (string) $lineNumber);
                        $line->setAttribute("hits", (string) max(0, $hits));
                        $lines->appendChild($line);
                    }
                    $method->appendChild($lines);
                }

                $coveragePercent = ($totalLinesClass === 0) ? 0 : (int) (($coveredLinesClass / $totalLinesClass) * 100);
                $class->setAttribute("line-rate", (string) $coveragePercent);
                $class->appendChild($methods);

                $classes->appendChild($class);
            }

            $package->appendChild($classes);
            $packages->appendChild($package);
        }
        $coverage->appendChild($packages);

        return (string) $document->saveXML();
    }

    public function getOutputFileName(string $folder): string
    {
        return "$folder/{$this->baseFileName}";
    }

    public function setOutputFileName(string $baseFileName): void
    {
        $this->baseFileName = $baseFileName;
    }
}
