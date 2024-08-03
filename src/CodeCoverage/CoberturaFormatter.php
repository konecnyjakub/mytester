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
        $document->encoding = "UTF-8";
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
                $classLines = $this->getElementLines($reflectionClass, $reportFile->data);
                $totalLines = count($classLines);
                $coveredLines = $this->getCoveredLineCount($classLines);
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
                    $methodLines = $this->getElementLines($reflectionMethod, $reportFile->data);
                    $totalLines = count($methodLines);
                    $coveredLines = $this->getCoveredLineCount($methodLines);
                    $coveragePercent = ($totalLines === 0) ? 0 : (int) (($coveredLines / $totalLines) * 100);

                    $method = $this->createMethodElement(
                        $document,
                        $reflectionMethod->getName(),
                        "",
                        $coveragePercent,
                        0
                    );

                    $lines = $document->createElement("lines");
                    foreach ($methodLines as $lineNumber => $hits) {
                        $lines->appendChild($this->createLineElement($document, $lineNumber, $hits));
                    }
                    $method->appendChild($lines);

                    $methods->appendChild($method);
                }
                $class->appendChild($methods);

                $lines = $document->createElement("lines");
                foreach ($classLines as $lineNumber => $hits) {
                    $lines->appendChild($this->createLineElement($document, $lineNumber, $hits));
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
                $functionsLines = $document->createElement("lines");

                $methods = $document->createElement("methods");
                foreach ($reportFile->functions as $function) {
                    $functionLines = $this->getElementLines($function, $reportFile->data);
                    ksort($functionLines);
                    $totalLines = count($functionLines);
                    $totalLinesClass += $totalLines;
                    $coveredLines = $this->getCoveredLineCount($functionLines);
                    $coveredLinesClass += $coveredLines;
                    $coveragePercent = ($totalLines === 0) ? 0 : (int) (($coveredLines / $totalLines) * 100);

                    $method = $this->createMethodElement($document, $function->getName(), "", $coveragePercent, 0);
                    $lines = $document->createElement("lines");
                    foreach ($functionLines as $lineNumber => $hits) {
                        $lines->appendChild($this->createLineElement($document, $lineNumber, $hits));
                        $functionsLines->appendChild($this->createLineElement($document, $lineNumber, $hits));
                    }
                    $method->appendChild($lines);
                    $methods->appendChild($method);
                }

                $methods->appendChild($functionsLines);
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

    private function getElementLines(\ReflectionClass|\ReflectionFunctionAbstract $reflection, array $data): array
    {
        return array_filter($data, function (int $line) use ($reflection) {
            return ($line >= $reflection->getStartLine() && $line <= $reflection->getEndLine());
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getCoveredLineCount(array $lines): int
    {
        return count(array_filter($lines, function (int $value) {
            return $value > 0;
        }));
    }

    private function createMethodElement(
        \DOMDocument $document,
        string $name,
        string $signature,
        int $lineCoveragePercent,
        int $branchCoveragePercent
    ): \DOMElement {
        $method = $document->createElement("method");
        $method->setAttribute("name", $name);
        $method->setAttribute("signature", $signature);
        $method->setAttribute("line-rate", (string) $lineCoveragePercent);
        $method->setAttribute("branch-rate", (string) $branchCoveragePercent);
        return $method;
    }

    private function createLineElement(\DOMDocument $document, int $lineNumber, int $hits): \DOMElement
    {
        $line = $document->createElement("line");
        $line->setAttribute("number", (string) $lineNumber);
        $line->setAttribute("hits", (string) max(0, $hits));
        return $line;
    }
}
